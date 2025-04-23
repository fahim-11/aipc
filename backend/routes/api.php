<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProjectManagement\DashboardController;
use App\Http\Controllers\Api\ProjectManagement\FeedbackController;
use App\Http\Controllers\Api\ProjectManagement\FinalInspectionController;
use App\Http\Controllers\Api\ProjectManagement\InspectionController;
use App\Http\Controllers\Api\ProjectManagement\ProjectController;
use App\Http\Controllers\Api\ProjectManagement\ProjectReportController;
use App\Http\Controllers\Api\ProjectManagement\PublicController;
use App\Http\Controllers\Api\ProjectManagement\RetentionController;
use App\Http\Controllers\Api\ProjectManagement\StatusHistoryController;
use App\Http\Controllers\Api\ProjectManagement\ContractorController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('project_management')->group(function () {
    // Public Routes
    Route::prefix('public')->group(function () {
        Route::get('/projects', [PublicController::class, 'index']);
        Route::get('/projects/{id}', [PublicController::class, 'show']);
        Route::get('/search', [PublicController::class, 'search']);
    });

    Route::post('/feedback', [FeedbackController::class, 'store']);

    // Admin Routes
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::resource('contractors', ContractorController::class);
        Route::get('/project-report/dashboard-report', [ProjectReportController::class, 'dashboardReport']);
    });

    // Official (Internal User) Routes
    Route::middleware(['auth:sanctum', 'role:official'])->group(function () {
        Route::resource('projects', ProjectController::class);
        Route::resource('inspections', InspectionController::class);
        Route::resource('final-inspections', FinalInspectionController::class);
        Route::resource('retentions', RetentionController::class);
        Route::get('/project-report/project-detail/{project}', [ProjectReportController::class, 'projectDetailReport']);
        Route::get('/project-report/project-status-history/{project}', [ProjectReportController::class, 'projectStatusHistoryReport']);
        Route::get('/project-report/projects-by-phase/{phase}', [ProjectReportController::class, 'projectsByPhaseReport']);
        Route::get('/project-report/retention-work', [ProjectReportController::class, 'retentionWorkReport']);
        Route::get('/project-report/final-inspection', [ProjectReportController::class, 'finalInspectionReport']);
    });

    Route::get('/status-history/{projectId}', [StatusHistoryController::class, 'getByProject']);

    // Contractor Routes
    Route::middleware(['auth:sanctum', 'role:contractor'])->group(function () {
        Route::get('/contractor/my-projects', [ContractorController::class, 'myProjects']);
        Route::get('/contractor/progress-report', [ContractorController::class, 'progressReport']);
        Route::get('/contractor/retention-status', [ContractorController::class, 'retentionStatus']);
        Route::get('/contractor/final-inspection-feedback', [ContractorController::class, 'finalInspectionFeedback']);
    });

    // Dashboard Routes (Authentication only)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/dashboard/project-progress', [DashboardController::class, 'getProjectProgress']);
        Route::get('/dashboard/contractor-performance', [DashboardController::class, 'getContractorPerformance']);
        Route::get('/dashboard/top-5-delayed-projects', [DashboardController::class, 'getTop5DelayedProjects']);
        Route::get('/dashboard/projects-by-phase', [DashboardController::class, 'getProjectsByPhase']);
        Route::get('/dashboard/projects-by-region', [DashboardController::class, 'getProjectsByRegion']);
        Route::get('/dashboard/delayed-projects', [DashboardController::class, 'getDelayedProjects']);
        Route::get('/dashboard/upcoming-inspections', [DashboardController::class, 'getUpcomingInspections']);
    });
});