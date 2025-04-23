<?php

namespace App\Http\Controllers\Api\ProjectManagement;

use App\Http\Controllers\Controller;
use App\Models\RetentionFund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class RetentionController extends Controller
{
    /**
     * Store a newly created retention fund in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'amount_held' => 'required|numeric|min:0',
            'conditions_for_release' => 'required|string',
            'actual_release_status' => 'required|boolean',
            'withheld_date' => 'required|date',
            'released_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $retentionFund = RetentionFund::create($validator->validated());

        return response()->json(['message' => 'Retention fund created successfully', 'data' => $retentionFund], 201);
    }

    /**
     * Display the specified retention fund.
     *
     * @param  \App\Models\RetentionFund  $retentionFund
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(RetentionFund $retentionFund): JsonResponse
    {
        return response()->json($retentionFund);
    }

    /**
     * Update the specified retention fund in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RetentionFund  $retentionFund
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, RetentionFund $retentionFund): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'exists:projects,id',
            'amount_held' => 'numeric|min:0',
            'conditions_for_release' => 'string',
            'actual_release_status' => 'boolean',
            'withheld_date' => 'date',
            'released_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $retentionFund->update($validator->validated());

        return response()->json(['message' => 'Retention fund updated successfully', 'data' => $retentionFund], 200);
    }

    /**
     * Remove the specified retention fund from storage.
     *
     * @param  \App\Models\RetentionFund  $retentionFund
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(RetentionFund $retentionFund): JsonResponse
    {
        $retentionFund->delete();

        return response()->json(['message' => 'Retention fund deleted successfully'], 204);
    }

    /**
     * Get all retention funds.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $retentionFunds = RetentionFund::all();

        return response()->json($retentionFunds);
    }

    /**
     * Release the retention fund.
     *
     * @param  \App\Models\RetentionFund  $retentionFund
     * @return \Illuminate\Http\JsonResponse
     */
    public function release(RetentionFund $retentionFund): JsonResponse
    {
        $retentionFund->actual_release_status = true;
        $retentionFund->released_date = now();
        $retentionFund->save();

        return response()->json(['message' => 'Retention fund released successfully', 'data' => $retentionFund], 200);
    }
}