<?php

namespace App\Http\Controllers\Api\ProjectManagement;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PublicController extends Controller
{
    /**
     * Display a listing of publicly visible projects.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        // Adjust the query to include only publicly visible fields
        $projects = Project::select(['id', 'project_name', 'status', 'contractor_id', 'consultancy_id', 'start_date', 'end_date', 'location'])
            ->with(['contractor:id,contractor_name', 'consultancy:id,consultancy_name']) // Load only the name fields from related tables
            ->get();

        return response()->json($projects);
    }

    /**
     * Display the specified publicly visible project.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $project = Project::select(['id', 'project_name', 'status', 'contractor_id', 'consultancy_id', 'start_date', 'end_date', 'location'])
            ->with(['contractor:id,contractor_name,phone_number,email_address', 'consultancy:id,consultancy_name,phone_number,email_address']) // Load only the necessary fields
            ->findOrFail($id);

        return response()->json($project);
    }

    /**
     * Search projects by project name or contractor name.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $searchTerm = $request->input('search');

        $projects = Project::select(['id', 'project_name', 'status', 'contractor_id', 'consultancy_id', 'start_date', 'end_date', 'location'])
            ->where('project_name', 'like', "%{$searchTerm}%")
            ->orWhereHas('contractor', function ($query) use ($searchTerm) {
                $query->where('contractor_name', 'like', "%{$searchTerm}%");
            })
            ->with(['contractor:id,contractor_name', 'consultancy:id,consultancy_name'])
            ->get();

        return response()->json($projects);
    }
}