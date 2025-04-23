<?php

// AMHARA-IP-PROJECT/backend/app/Http/Controllers/Api/ProjectManagement/ProjectController.php

namespace App\Http\Controllers\Api\ProjectManagement;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Contractor;
use App\Models\Consultancy;
use App\Models\Milestone;
use App\Models\ProjectStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Events\ProjectCreated; // Ensure this class exists in the App\Events namespace
use App\Events\ProjectUpdated; // Ensure this class exists in the App\Events namespace

class ProjectController extends Controller
{
    /**
     * Display a listing of projects.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $projects = Project::with(['contractor', 'consultancy', 'milestones'])->get();
        return response()->json($projects);
    }

    /**
     * Store a newly created project in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'project_name' => 'required|string|max:255',
            'contractor_id' => 'required|exists:contractors,id',
            'consultancy_id' => 'required|exists:consultancies,id',
            'location' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'status' => 'required|in:Planning,Under Construction,Final Inspection,Completed',
            'phases' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        // Create the project
        $project = Project::create([
            'project_name' => $validatedData['project_name'],
            'contractor_id' => $validatedData['contractor_id'],
            'consultancy_id' => $validatedData['consultancy_id'],
            'location' => $validatedData['location'],
            'start_date' => $validatedData['start_date'],
            'end_date' => $validatedData['end_date'],
            'status' => $validatedData['status'],
        ]);

        // Create milestones for each phase
        foreach ($validatedData['phases'] as $phase) {
            foreach ($phase['milestones'] as $milestoneData) {
                Milestone::create([
                    'project_id' => $project->id,
                    'phase_name' => $phase['phase_name'],
                    'milestone_name' => $milestoneData['milestone_name'],
                ]);
            }
        }

        // Create status history
        ProjectStatusHistory::create([
            'project_id' => $project->id,
            'status' => $project->status,
            'changed_by' => Auth::id(), // Assuming user is authenticated
        ]);

        // Dispatch ProjectCreated event
        event(new ProjectCreated($project));

        return response()->json(['message' => 'Project created successfully', 'data' => $project], 201);
    }

    /**
     * Display the specified project.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Project $project): JsonResponse
    {
        $project->load(['contractor', 'consultancy', 'milestones', 'statusHistory']);
        return response()->json($project);
    }

    /**
     * Update the specified project in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Project $project): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'project_name' => 'string|max:255',
            'contractor_id' => 'exists:contractors,id',
            'consultancy_id' => 'exists:consultancies,id',
            'location' => 'string|max:255',
            'start_date' => 'date',
            'end_date' => 'date',
            'status' => 'in:Planning,Under Construction,Final Inspection,Completed',
            'phases' => 'array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        // Update the project
        $project->update([
            'project_name' => $validatedData['project_name'] ?? $project->project_name,
            'contractor_id' => $validatedData['contractor_id'] ?? $project->contractor_id,
            'consultancy_id' => $validatedData['consultancy_id'] ?? $project->consultancy_id,
            'location' => $validatedData['location'] ?? $project->location,
            'start_date' => $validatedData['start_date'] ?? $project->start_date,
            'end_date' => $validatedData['end_date'] ?? $project->end_date,
            'status' => $validatedData['status'] ?? $project->status,
        ]);

        // Update milestones if phases are provided
        if (isset($validatedData['phases'])) {
            // Delete existing milestones
            Milestone::where('project_id', $project->id)->delete();

            // Create new milestones
            foreach ($validatedData['phases'] as $phase) {
                foreach ($phase['milestones'] as $milestoneData) {
                    Milestone::create([
                        'project_id' => $project->id,
                        'phase_name' => $phase['phase_name'],
                        'milestone_name' => $milestoneData['milestone_name'],
                    ]);
                }
            }
        }

        // Create status history if status is updated
        if (isset($validatedData['status']) && $validatedData['status'] !== $project->status) {
            ProjectStatusHistory::create([
                'project_id' => $project->id,
                'status' => $validatedData['status'],
                'changed_by' => Auth::id(), // Assuming user is authenticated
            ]);
        }

        // Dispatch ProjectUpdated event
        event(new ProjectUpdated($project));

        return response()->json(['message' => 'Project updated successfully', 'data' => $project], 200);
    }

    /**
     * Remove the specified project from storage.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Project $project): JsonResponse
    {
        // Delete milestones associated with the project
        Milestone::where('project_id', $project->id)->delete();

        // Delete status history associated with the project
        ProjectStatusHistory::where('project_id', $project->id)->delete();

        $project->delete();

        return response()->json(['message' => 'Project deleted successfully'], 204);
    }

    /**
     * Get projects by phase.
     *
     * @param  string  $phase
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProjectsByPhase(string $phase): JsonResponse
    {
        $projects = Project::where('status', $phase)->get();
        return response()->json($projects);
    }
}