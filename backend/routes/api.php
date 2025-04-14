<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProjectManagement\ProjectController;
use App\Http\Controllers\Api\ProjectManagement\RetentionController;
use App\Http\Controllers\Api\ProjectManagement\InspectionController;
use App\Http\Controllers\Api\ProjectManagement\FeedbackController;
use App\Http\Controllers\Api\ProjectManagement\PublicController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// --- Construction Project Management Module ---
Route::prefix('project_management')->name('project_management.')->group(function () {

    // --- Internal Routes (Assume Authentication/Authorization handled by middleware group if needed) ---

    // Project CRUD & Status
    Route::apiResource('projects', ProjectController::class);
    Route::put('projects/{project}/status', [ProjectController::class, 'updateStatus'])->name('projects.updateStatus');

    // Retention Management (Associated with a Project)
    Route::get('projects/{project}/retention', [RetentionController::class, 'show'])->name('retention.show');
    Route::post('projects/{project}/retention', [RetentionController::class, 'storeOrUpdate'])->name('retention.storeOrUpdate'); // Use POST for create/update simplicity
    Route::put('projects/{project}/retention', [RetentionController::class, 'storeOrUpdate'])->name('retention.update'); // Alias PUT

    // Inspection Management (Associated with a Project)
    Route::get('projects/{project}/inspections', [InspectionController::class, 'index'])->name('inspections.index');
    Route::post('projects/{project}/inspections', [InspectionController::class, 'store'])->name('inspections.store');
    Route::get('projects/{project}/inspections/{inspectionReport}', [InspectionController::class, 'show'])->name('inspections.show');
    Route::delete('projects/{project}/inspections/{inspectionReport}', [InspectionController::class, 'destroy'])->name('inspections.destroy');

    // Feedback Management (Admin view/update)
    Route::get('feedback', [FeedbackController::class, 'index'])->name('feedback.index.admin');
    Route::get('feedback/{feedback}', [FeedbackController::class, 'show'])->name('feedback.show.admin');
    Route::put('feedback/{feedback}', [FeedbackController::class, 'update'])->name('feedback.update.admin');
    Route::delete('feedback/{feedback}', [FeedbackController::class, 'destroy'])->name('feedback.destroy.admin');


    // --- Public Routes ---

    // Public Project Dashboard
    Route::get('public_projects', [PublicController::class, 'publicProjects'])->name('public.projects');

    // Public Feedback Submission
    Route::post('feedback', [FeedbackController::class, 'store'])->name('feedback.store.public');

}); // End of project_management prefix group