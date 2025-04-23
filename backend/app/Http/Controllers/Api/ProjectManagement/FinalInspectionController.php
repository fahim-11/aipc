<?php

namespace App\Http\Controllers\Api\ProjectManagement;

use App\Http\Controllers\Controller;
use App\Models\InspectionReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class FinalInspectionController extends Controller
{
    /**
     * Store a newly created inspection report in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'inspection_date' => 'required|date',
            'team_inspector_name' => 'required|string|max:255',
            'findings' => 'required|string',
            'supporting_files' => 'nullable|file|mimes:pdf,doc,docx|max:2048', // Adjust mime types and size as needed
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        if ($request->hasFile('supporting_files')) {
            $file = $request->file('supporting_files');
            $path = $file->store('inspection_reports', 'public'); // Store in storage/app/public/inspection_reports
            $validatedData['supporting_files'] = $path;
        }

        $inspectionReport = InspectionReport::create($validatedData);

        return response()->json(['message' => 'Inspection report created successfully', 'data' => $inspectionReport], 201);
    }

    /**
     * Display the specified inspection report.
     *
     * @param  \App\Models\InspectionReport  $inspectionReport
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(InspectionReport $inspectionReport): JsonResponse
    {
        return response()->json($inspectionReport);
    }

    /**
     * Update the specified inspection report in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\InspectionReport  $inspectionReport
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, InspectionReport $inspectionReport): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'exists:projects,id',
            'inspection_date' => 'date',
            'team_inspector_name' => 'string|max:255',
            'findings' => 'string',
            'supporting_files' => 'nullable|file|mimes:pdf,doc,docx|max:2048', // Adjust mime types and size as needed
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        if ($request->hasFile('supporting_files')) {
            // Delete the old file if it exists
            if ($inspectionReport->supporting_files) {
                Storage::disk('public')->delete($inspectionReport->supporting_files);
            }

            $file = $request->file('supporting_files');
            $path = $file->store('inspection_reports', 'public'); // Store in storage/app/public/inspection_reports
            $validatedData['supporting_files'] = $path;
        }

        $inspectionReport->update($validatedData);

        return response()->json(['message' => 'Inspection report updated successfully', 'data' => $inspectionReport], 200);
    }

    /**
     * Remove the specified inspection report from storage.
     *
     * @param  \App\Models\InspectionReport  $inspectionReport
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(InspectionReport $inspectionReport): JsonResponse
    {
        // Delete the file if it exists
        if ($inspectionReport->supporting_files) {
            Storage::disk('public')->delete($inspectionReport->supporting_files);
        }

        $inspectionReport->delete();

        return response()->json(['message' => 'Inspection report deleted successfully'], 204);
    }

    /**
     * Get all inspection reports.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $inspectionReports = InspectionReport::all();

        return response()->json($inspectionReports);
    }

    /**
     * Display a listing of the failed inspections.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function failedInspections(): JsonResponse
    {
        $failedInspections = InspectionReport::where('findings', 'like', '%fail%')->get(); // Assuming "fail" keyword indicates failure

        return response()->json($failedInspections);
    }

    /**
     * Display a listing of the passed inspections.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function passedInspections(): JsonResponse
    {
        $passedInspections = InspectionReport::where('findings', 'like', '%pass%')->get(); // Assuming "pass" keyword indicates success

        return response()->json($passedInspections);
    }
}