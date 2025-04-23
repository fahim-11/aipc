<?php

namespace App\Http\Controllers\Api\ProjectManagement;

// AMHARA-IP-PROJECT/backend/app/Http/Controllers/Api/ProjectManagement/StatusHistoryController.php

use App\Http\Controllers\Controller;
use App\Models\ProjectStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class StatusHistoryController extends Controller
{
    /**
     * Display a listing of status history entries.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $statusHistory = ProjectStatusHistory::with('project', 'user')->get();
        return response()->json($statusHistory);
    }

    /**
     * Store a newly created status history entry in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'status' => 'required|string|max:255',
            'changed_by' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $statusHistory = ProjectStatusHistory::create($validator->validated());

        return response()->json($statusHistory, 201);
    }

    /**
     * Display the specified status history entry.
     *
     * @param  \App\Models\ProjectStatusHistory  $projectStatusHistory
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ProjectStatusHistory $projectStatusHistory): JsonResponse
    {
        $projectStatusHistory->load('project', 'user');
        return response()->json($projectStatusHistory);
    }

    /**
     * Update the specified status history entry in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProjectStatusHistory  $projectStatusHistory
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, ProjectStatusHistory $projectStatusHistory): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'exists:projects,id',
            'status' => 'string|max:255',
            'changed_by' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $projectStatusHistory->update($validator->validated());

        return response()->json($projectStatusHistory, 200);
    }

    /**
     * Remove the specified status history entry from storage.
     *
     * @param  \App\Models\ProjectStatusHistory  $projectStatusHistory
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ProjectStatusHistory $projectStatusHistory): JsonResponse
    {
        $projectStatusHistory->delete();
        return response()->json(null, 204);
    }

    /**
     * Get status history by project ID.
     *
     * @param  int  $projectId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByProject(int $projectId): JsonResponse
    {
        $statusHistory = ProjectStatusHistory::where('project_id', $projectId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($statusHistory);
    }
}