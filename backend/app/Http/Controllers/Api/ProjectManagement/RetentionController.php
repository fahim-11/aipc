<?php
namespace App\Http\Controllers\Api\ProjectManagement;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\RetentionFund;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RetentionController extends Controller
{
    // Get retention for a specific project
    public function show(Project $project) {
         return $project->retentionFund()->firstOrFail();
    }

    // Store or Update retention for a specific project
    public function storeOrUpdate(Request $request, Project $project)
    {
        $validated = $request->validate([
            'amount_held' => 'required|numeric|min:0',
            'release_conditions' => 'nullable|string',
            'status' => ['required', Rule::in(['Held', 'Released'])],
            'release_date' => 'nullable|required_if:status,Released|date',
        ]);

        $retention = RetentionFund::updateOrCreate(
            ['project_id' => $project->id],
            $validated
        );

        return response()->json($retention, $retention->wasRecentlyCreated ? 201 : 200);
    }
}