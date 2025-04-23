<?php

namespace App\Http\Controllers\Api\ProjectManagement;

// AMHARA-IP-PROJECT/backend/app/Http/Controllers/Api/ProjectManagement/FeedbackController.php

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class FeedbackController extends Controller
{
    /**
     * Store a newly created feedback in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'complaint_type' => 'required|string|max:255',
            'description' => 'required|string',
            'contact_email' => 'nullable|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $feedback = Feedback::create($validator->validated());

        return response()->json(['message' => 'Feedback submitted successfully', 'data' => $feedback], 201);
    }

    /**
     * Display the specified feedback.
     *
     * @param  \App\Models\Feedback  $feedback
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Feedback $feedback): JsonResponse
    {
        return response()->json($feedback);
    }

    /**
     * Update the specified feedback in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Feedback  $feedback
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Feedback $feedback): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'complaint_type' => 'string|max:255',
            'description' => 'string',
            'contact_email' => 'nullable|email',
            'status' => 'in:new,in progress,resolved,closed', // Added status field
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $feedback->update($validator->validated());

        return response()->json(['message' => 'Feedback updated successfully', 'data' => $feedback], 200);
    }

    /**
     * Remove the specified feedback from storage.
     *
     * @param  \App\Models\Feedback  $feedback
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Feedback $feedback): JsonResponse
    {
        $feedback->delete();

        return response()->json(['message' => 'Feedback deleted successfully'], 204);
    }

    /**
     * Get all feedback messages.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $feedback = Feedback::all();

        return response()->json($feedback);
    }

    /**
     * Resolve the specified feedback.
     *
     * @param  \App\Models\Feedback  $feedback
     * @return \Illuminate\Http\JsonResponse
     */
    public function resolve(Feedback $feedback): JsonResponse
    {
        $feedback->status = 'resolved';
        $feedback->save();

        return response()->json(['message' => 'Feedback resolved successfully', 'data' => $feedback], 200);
    }
}