<?php

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
use App\Events\ProjectCreated;
use App\Events\ProjectUpdated;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    public function index(): JsonResponse
    {
        $projects = Project::with(['contractor', 'consultancy', 'milestones'])->get();
        return response()->json($projects);
    }

    public function store(Request $request): JsonResponse
    {
        Log::info('ProjectController::store() - Request Data:', $request->all());

        $validator = Validator::make($request->all(), [
            'project_name' => 'required|string|max:255',
            'contractor_id' => 'required|integer|exists:contractors,id',  // Ensure integer and exists
            'consultancy_id' => 'required|integer|exists:consultancies,id', // Ensure integer and exists
            'location' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'status' => 'required|in:Planning,Under Construction,Final Inspection,Completed',
            'phases' => 'required|array',
            'phases.*.phase_name' => 'required|string',
            'phases.*.milestones' => 'required|array',
            'phases.*.milestones.*.milestone_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            Log::error('ProjectController::store() - Validation Errors:', $validator->errors()->toArray());
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        $project = Project::create([
            'project_name' => $validatedData['project_name'],
            'contractor_id' => $validatedData['contractor_id'],
            'consultancy_id' => $validatedData['consultancy_id'],
            'location' => $validatedData['location'],
            'start_date' => $validatedData['start_date'],
            'end_date' => $validatedData['end_date'],
            'status' => $validatedData['status'],
        ]);

        foreach ($validatedData['phases'] as $phase) {
            foreach ($phase['milestones'] as $milestoneData) {
                Milestone::create([
                    'project_id' => $project->id,
                    'phase_name' => $phase['phase_name'],
                    'milestone_name' => $milestoneData['milestone_name'],
                ]);
            }
        }

        ProjectStatusHistory::create([
            'project_id' => $project->id,
            'status' => $project->status,
            'changed_by' => Auth::id(),
        ]);

        event(new ProjectCreated($project));

        return response()->json(['message' => 'Project created successfully', 'data' => $project], 201);
    }

    public function show(Project $project): JsonResponse
    {
        $project->load(['contractor', 'consultancy', 'milestones', 'statusHistory']);
        return response()->json($project);
    }

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
            'phases.*.phase_name' => 'string',
            'phases.*.milestones' => 'array',
            'phases.*.milestones.*.milestone_name' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        $project->update([
            'project_name' => $validatedData['project_name'] ?? $project->project_name,
            'contractor_id' => $validatedData['contractor_id'] ?? $project->contractor_id,
            'consultancy_id' => $validatedData['consultancy_id'] ?? $project->consultancy_id,
            'location' => $validatedData['location'] ?? $project->location,
            'start_date' => $validatedData['start_date'] ?? $project->start_date,
            'end_date' => $validatedData['end_date'] ?? $project->end_date,
            'status' => $validatedData['status'] ?? $project->status,
        ]);

        if (isset($validatedData['phases'])) {
            Milestone::where('project_id', $project->id)->delete();
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

        if (isset($validatedData['status']) && $validatedData['status'] !== $project->status) {
            ProjectStatusHistory::create([
                'project_id' => $project->id,
                'status' => $validatedData['status'],
                'changed_by' => Auth::id(),
            ]);
        }

        event(new ProjectUpdated($project));

        return response()->json(['message' => 'Project updated successfully', 'data' => $project], 200);
    }

    public function destroy(Project $project): JsonResponse
    {
        Milestone::where('project_id', $project->id)->delete();
        ProjectStatusHistory::where('project_id', $project->id)->delete();
        $project->delete();
        return response()->json(['message' => 'Project deleted successfully'], 204);
    }

    public function getProjectsByPhase(string $phase): JsonResponse
    {
        $projects = Project::where('status', $phase)->get();
        return response()->json($projects);
    }
}