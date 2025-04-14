<?php
namespace App\Http\Controllers\Api\ProjectManagement;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\InspectionReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InspectionController extends Controller
{
     // Get inspections for a specific project
     public function index(Project $project) {
        return $project->inspectionReports()->orderBy('inspection_date', 'desc')->get();
    }

    // Store a new inspection report
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'inspection_date' => 'required|date',
            'inspector_name' => 'required|string|max:255',
            'findings' => 'required|string',
            'report_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:10240', // 10MB Max
        ]);

        $filePath = null;
        $originalName = null;
        if ($request->hasFile('report_file')) {
            $file = $request->file('report_file');
            $originalName = $file->getClientOriginalName();
            // Store in 'public/inspection_reports/{project_id}/filename.ext'
            $filePath = $file->store("inspection_reports/{$project->id}", 'public');
        }

        $inspection = InspectionReport::create([
            'project_id' => $project->id,
            'inspection_date' => $validated['inspection_date'],
            'inspector_name' => $validated['inspector_name'],
            'findings' => $validated['findings'],
            'report_file_path' => $filePath,
            'original_file_name' => $originalName,
        ]);

        // Load the accessor URL before returning
        $inspection->load('project'); // Optional: if needed on frontend
        $inspection->refresh(); // Ensure accessor is loaded


        return response()->json($inspection->append('report_file_url'), 201);
    }

     // Get a specific inspection report
     public function show(Project $project, InspectionReport $inspectionReport) {
         // Ensure the report belongs to the project (implicitly handled by route model binding setup)
         if ($inspectionReport->project_id !== $project->id) {
             abort(404);
         }
         return $inspectionReport->append('report_file_url');
     }

      // (Optional) Delete an inspection report
     public function destroy(Project $project, InspectionReport $inspectionReport) {
         if ($inspectionReport->project_id !== $project->id) {
             abort(404);
         }

         // Delete file from storage if it exists
         if ($inspectionReport->report_file_path) {
             Storage::disk('public')->delete($inspectionReport->report_file_path);
         }

         $inspectionReport->delete();
         return response()->json(null, 204);
     }
}