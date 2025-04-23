<?php

// AMHARA-IP-PROJECT/backend/app/Http/Controllers/Api/ProjectManagement/InspectionController.php

namespace App\Http\Controllers\Api\ProjectManagement;

use App\Http\Controllers\Controller;
use App\Models\InspectionReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class InspectionController extends Controller
{
    /**
     * Display a listing of inspection activities.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $inspections = InspectionReport::all();
        return response()->json($inspections);
    }

    /**
     * Store a newly created inspection activity in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'inspection_date' => 'required|date',
            'team_inspector_name' => 'required|string',
            'findings' => 'required|string',
            'supporting_files' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        if ($request->hasFile('supporting_files')) {
            $file = $request->file('supporting_files');
            $path = $file->store('inspection_reports', 'public');
            $validatedData['supporting_files'] = $path;
        }

        $inspection = InspectionReport::create($validatedData);

        return response()->json($inspection, 201);
    }

    /**
     * Display the specified inspection activity.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $inspection = InspectionReport::findOrFail($id);
        return response()->json($inspection);
    }

    /**
     * Update the specified inspection activity in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $inspection = InspectionReport::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'project_id' => 'exists:projects,id',
            'inspection_date' => 'date',
            'team_inspector_name' => 'string',
            'findings' => 'string',
            'supporting_files' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        if ($request->hasFile('supporting_files')) {
            if ($inspection->supporting_files) {
                Storage::disk('public')->delete($inspection->supporting_files);
            }

            $file = $request->file('supporting_files');
            $path = $file->store('inspection_reports', 'public');
            $validatedData['supporting_files'] = $path;
        }

        $inspection->update($validatedData);

        return response()->json($inspection, 200);
    }

    /**
     * Remove the specified inspection activity from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $inspection = InspectionReport::findOrFail($id);

        if ($inspection->supporting_files) {
            Storage::disk('public')->delete($inspection->supporting_files);
        }

        $inspection->delete();

        return response()->json(null, 204);
    }
}