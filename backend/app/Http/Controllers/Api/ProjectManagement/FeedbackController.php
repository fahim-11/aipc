<?php
namespace App\Http\Controllers\Api\ProjectManagement;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FeedbackController extends Controller
{
    public function index()
    {
        return Feedback::orderBy('created_at', 'desc')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:255', // Added project_name validation
            'complaint_type' => 'required|string|max:100',
            'description' => 'required|string|max:2000',
            'contact_info' => 'nullable|string|max:255',
        ]);

        $validated['status'] = 'New';
        $feedback = Feedback::create($validated);

        return response()->json(['message' => 'Feedback submitted successfully.', 'feedback' => $feedback], 201);
    }

    public function show(Feedback $feedback)
    {
        return $feedback;
    }

    public function update(Request $request, Feedback $feedback)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['New', 'In Progress', 'Resolved'])],
        ]);
        $feedback->update($validated);
        return response()->json($feedback);
    }

    public function destroy(Feedback $feedback) {
        $feedback->delete();
        return response()->json(null, 204);
    }
}