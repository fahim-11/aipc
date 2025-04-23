<?php

namespace App\Http\Controllers\Api\ProjectManagement;

use App\Http\Controllers\Controller;
use App\Models\Contractor;
use App\Models\User;
use App\Models\InspectionReport; // Add this line
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ContractorController extends Controller
{
    /**
     * Display a listing of contractors.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $contractors = Contractor::all();
        return response()->json($contractors);
    }

    /**
     * Store a newly created contractor in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'contractor_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email_address' => 'required|email|unique:contractors,email_address',
            'company_address' => 'required|string|max:255',
            'unique_id' => 'required|string|max:255|unique:contractors,unique_id',
            'password' => 'nullable|string|min:8', // Optional password
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        // Create contractor
        $contractor = Contractor::create([
            'contractor_name' => $validatedData['contractor_name'],
            'phone_number' => $validatedData['phone_number'],
            'email_address' => $validatedData['email_address'],
            'company_address' => $validatedData['company_address'],
            'unique_id' => $validatedData['unique_id'],
        ]);

        // If a password is provided, create a user account as well
        if (isset($validatedData['password'])) {
            $user = User::create([
                'name' => $validatedData['contractor_name'],
                'email' => $validatedData['email_address'],
                'password' => Hash::make($validatedData['password']),
                'role' => 'contractor', // Set the role to contractor
                'contractor_id' => $contractor->id, // Link the user to the contractor
            ]);
        }

        return response()->json(['message' => 'Contractor created successfully', 'data' => $contractor], 201);
    }

    /**
     * Display the specified contractor.
     *
     * @param  \App\Models\Contractor  $contractor
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Contractor $contractor): JsonResponse
    {
        return response()->json($contractor);
    }

    /**
     * Update the specified contractor in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Contractor  $contractor
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Contractor $contractor): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'contractor_name' => 'string|max:255',
            'phone_number' => 'string|max:20',
            'email_address' => 'email|unique:contractors,email_address,' . $contractor->id,
            'company_address' => 'string|max:255',
            'unique_id' => 'string|max:255|unique:contractors,unique_id,' . $contractor->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $contractor->update($validator->validated());

        return response()->json(['message' => 'Contractor updated successfully', 'data' => $contractor], 200);
    }

    /**
     * Remove the specified contractor from storage.
     *
     * @param  \App\Models\Contractor  $contractor
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Contractor $contractor): JsonResponse
    {
        $contractor->delete();

        return response()->json(['message' => 'Contractor deleted successfully'], 204);
    }

     /**
     * Get projects assigned to the logged-in contractor.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function myProjects(): JsonResponse
    {
        // Check if the user is authenticated and has a contractor role
        if (Auth::check() && Auth::user()->role === 'contractor') {
            $contractor = Auth::user()->contractor; // Assuming you have a relationship defined in the User model

            // Retrieve projects assigned to the contractor
            $projects = \App\Models\Project::where('contractor_id', $contractor->id)->get();

            return response()->json($projects);
        }

        return response()->json(['message' => 'Unauthorized'], 403); // Or another appropriate error code
    }

    /**
     * Get progress reports for the contractor's projects.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function progressReport(): JsonResponse
    {
        // Check if the user is authenticated and has a contractor role
        if (Auth::check() && Auth::user()->role === 'contractor') {
            $contractor = Auth::user()->contractor; // Assuming you have a relationship defined in the User model

            // Retrieve projects assigned to the contractor
            $projects = \App\Models\Project::where('contractor_id', $contractor->id)->get();

            // Prepare the progress report data
            $progressReport = [];
            foreach ($projects as $project) {
                $progressReport[] = [
                    'project_name' => $project->project_name,
                    'milestones' => $project->milestones,
                    'current_status' => $project->status,
                    'updated_date' => $project->updated_at,
                ];
            }

            return response()->json($progressReport);
        }

        return response()->json(['message' => 'Unauthorized'], 403); // Or another appropriate error code
    }

    /**
     * Get retention status for the contractor's projects.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function retentionStatus(): JsonResponse
    {
        // Check if the user is authenticated and has a contractor role
        if (Auth::check() && Auth::user()->role === 'contractor') {
            $contractor = Auth::user()->contractor; // Assuming you have a relationship defined in the User model

            // Retrieve retention funds associated with the contractor's projects
            $retentionFunds = \App\Models\RetentionFund::whereHas('project', function ($query) use ($contractor) {
                $query->where('contractor_id', $contractor->id);
            })->get();

            // Prepare the retention status data
            $retentionStatus = [];
            foreach ($retentionFunds as $retentionFund) {
                $retentionStatus[] = [
                    'project_name' => $retentionFund->project->project_name,
                    'retention_amount' => $retentionFund->amount_held,
                    'conditions' => $retentionFund->conditions_for_release,
                    'held_date' => $retentionFund->withheld_date,
                    'released_status' => $retentionFund->actual_release_status,
                ];
            }

            return response()->json($retentionStatus);
        }

        return response()->json(['message' => 'Unauthorized'], 403); // Or another appropriate error code
    }

    /**
     * Get final inspection feedback for the contractor's projects.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function finalInspectionFeedback(): JsonResponse
    {
        // Check if the user is authenticated and has a contractor role
        if (Auth::check() && Auth::user()->role === 'contractor') {
            $contractor = Auth::user()->contractor; // Assuming you have a relationship defined in the User model

            // Retrieve inspection reports associated with the contractor's projects
            $inspectionReports = InspectionReport::whereHas('project', function ($query) use ($contractor) {
                $query->where('contractor_id', $contractor->id);
            })->get();

            // Prepare the inspection feedback data
            $inspectionFeedback = [];
            foreach ($inspectionReports as $inspectionReport) {
                $inspectionFeedback[] = [
                    'project_name' => $inspectionReport->project->project_name,
                    'inspection_date' => $inspectionReport->inspection_date,
                    'inspector_name' => $inspectionReport->team_inspector_name,
                    'findings' => $inspectionReport->findings,
                    'attachments' => $inspectionReport->supporting_files,
                ];
            }

            return response()->json($inspectionFeedback);
        }

        return response()->json(['message' => 'Unauthorized'], 403); // Or another appropriate error code
    }
}