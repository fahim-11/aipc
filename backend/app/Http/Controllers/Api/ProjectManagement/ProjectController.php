<?php
namespace App\Http\Controllers\Api\ProjectManagement;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
// use App\Events\ProjectCreated; // Uncomment for real-time
// use App\Events\ProjectUpdated; // Uncomment for real-time

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        // Add pagination or filtering as needed
        return Project::orderBy('created_at', 'desc')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contractor_name' => 'required|string|max:255',
            'contractor_contact' => 'nullable|string|max:255',
            'consultancy_name' => 'required|string|max:255',
            'consultancy_contact' => 'nullable|string|max:255',
            'location' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => ['nullable', Rule::in(['Planning', 'Under Construction', 'Final Inspection', 'Completed'])],
            'phases_milestones_details' => 'nullable|string',
        ]);

        $project = Project::create($validated);

        // Placeholder: Handle milestones if sent differently (e.g., array)
        // if ($request->has('milestones')) { ... }

        // broadcast(new ProjectCreated($project))->toOthers(); // Uncomment for real-time
        return response()->json($project, 201);
    }

    public function show(Project $project)
    {
         // Eager load relations if needed frequently
         // $project->load(['milestones', 'retentionFund', 'inspectionReports']);
        return $project;
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'contractor_name' => 'sometimes|required|string|max:255',
            'contractor_contact' => 'nullable|string|max:255',
            'consultancy_name' => 'sometimes|required|string|max:255',
            'consultancy_contact' => 'nullable|string|max:255',
            'location' => 'sometimes|required|string|max:255',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after_or_equal:start_date',
            'status' => ['sometimes','required', Rule::in(['Planning', 'Under Construction', 'Final Inspection', 'Completed'])],
            'phases_milestones_details' => 'nullable|string',
        ]);

        $project->update($validated);

         // broadcast(new ProjectUpdated($project))->toOthers(); // Uncomment for real-time

        return response()->json($project);
    }

    public function destroy(Project $project)
    {
        // Add authorization checks here
        $project->delete();
        return response()->json(null, 204);
    }

    // Maybe a dedicated status update method
    public function updateStatus(Request $request, Project $project) {
         $validated = $request->validate([
            'status' => ['required', Rule::in(['Planning', 'Under Construction', 'Final Inspection', 'Completed'])],
        ]);
        $project->update(['status' => $validated['status']]);
         // broadcast(new ProjectUpdated($project))->toOthers(); // Uncomment for real-time
        return response()->json($project);
    }
}