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
use App\Http\Controllers\Api\AuthController;
use App\Http\Middleware\CheckRole;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
*/

// Default Sanctum route (keep outside project_management group if needed)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Main Project Management Routes Group
Route::prefix('project_management')->group(function () {
    // Authentication Routes
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    // Public Routes (no authentication)
    Route::prefix('public')->group(function () {
        Route::get('/projects', [PublicController::class, 'index']);
        Route::get('/projects/{id}', [PublicController::class, 'show']);
        Route::get('/search', [PublicController::class, 'search']);
    });

    // Feedback Route
    Route::post('/feedback', [FeedbackController::class, 'store']);

    // Admin Protected Routes
    Route::middleware(['auth:sanctum', CheckRole::class . ':admin'])->group(function () {
        Route::resource('contractors', ContractorController::class);
        Route::get('/project-report/dashboard-report', [ProjectReportController::class, 'dashboardReport']);
    });

    // Official Protected Routes
    Route::middleware(['auth:sanctum', CheckRole::class . ':official'])->group(function () {
        Route::resource('projects', ProjectController::class);
        Route::resource('inspections', InspectionController::class);
        Route::resource('final-inspections', FinalInspectionController::class);
        Route::resource('retentions', RetentionController::class);
        
        // Project Reports
        Route::prefix('project-report')->group(function () {
            Route::get('/project-detail/{project}', [ProjectReportController::class, 'projectDetailReport']);
            Route::get('/project-status-history/{project}', [ProjectReportController::class, 'projectStatusHistoryReport']);
            Route::get('/projects-by-phase/{phase}', [ProjectReportController::class, 'projectsByPhaseReport']);
            Route::get('/retention-work', [ProjectReportController::class, 'retentionWorkReport']);
            Route::get('/final-inspection', [ProjectReportController::class, 'finalInspectionReport']);
        });
    });

    // Status History (accessible to authenticated users)
    Route::get('/status-history/{projectId}', [StatusHistoryController::class, 'getByProject'])->middleware('auth:sanctum');

    // Contractor Protected Routes
    Route::middleware(['auth:sanctum', CheckRole::class . ':contractor'])->group(function () {
        Route::prefix('contractor')->group(function () {
            Route::get('/my-projects', [ContractorController::class, 'myProjects']);
            Route::get('/progress-report', [ContractorController::class, 'progressReport']);
            Route::get('/retention-status', [ContractorController::class, 'retentionStatus']);
            Route::get('/final-inspection-feedback', [ContractorController::class, 'finalInspectionFeedback']);
        });
    });

    // Dashboard Routes (authenticated users)
    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('dashboard')->group(function () {
            Route::get('/project-progress', [DashboardController::class, 'getProjectProgress']);
            Route::get('/contractor-performance', [DashboardController::class, 'getContractorPerformance']);
            Route::get('/top-5-delayed-projects', [DashboardController::class, 'getTop5DelayedProjects']);
            Route::get('/projects-by-phase', [DashboardController::class, 'getProjectsByPhase']);
            Route::get('/projects-by-region', [DashboardController::class, 'getProjectsByRegion']);
            Route::get('/delayed-projects', [DashboardController::class, 'getDelayedProjects']);
            Route::get('/upcoming-inspections', [DashboardController::class, 'getUpcomingInspections']);
        });
    });
});