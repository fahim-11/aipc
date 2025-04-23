<?php

namespace App\Http\Controllers\Api\ProjectManagement;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\DashboardMetric;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\InspectionReport;

class DashboardController extends Controller
{
    /**
     * Get project progress dashboard data.
     *
     * @return JsonResponse
     */
    public function getProjectProgress(): JsonResponse
    {
        $totalProjects = Project::count();
        $projectsByPhase = Project::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status')
            ->toArray();

        $projectsByRegion = Project::select('location', DB::raw('count(*) as total'))
            ->groupBy('location')
            ->get()
            ->pluck('total', 'location')
            ->toArray();

        $projectsByContractor = Project::with('contractor')
            ->select('contractor_id', DB::raw('count(*) as total'))
            ->groupBy('contractor_id')
            ->get()
            ->pluck('total', 'contractor.contractor_name')
            ->toArray();

        return response()->json([
            'totalProjects' => $totalProjects,
            'projectsByPhase' => $projectsByPhase,
            'projectsByRegion' => $projectsByRegion,
            'projectsByContractor' => $projectsByContractor,
        ]);
    }

    /**
     * Get contractor performance report data.
     *
     * @return JsonResponse
     */
    public function getContractorPerformance(): JsonResponse
    {
        $contractorPerformance = Project::select('contractor_id', DB::raw('count(*) as total_projects'), DB::raw('SUM(CASE WHEN status = "Completed" THEN 1 ELSE 0 END) as completed_projects'))
            ->groupBy('contractor_id')
            ->with('contractor')
            ->get();

        return response()->json($contractorPerformance);
    }

    /**
     * Get top 5 delayed projects.
     *
     * @return JsonResponse
     */
    public function getTop5DelayedProjects(): JsonResponse
    {
        $top5DelayedProjects = Project::where('status', '!=', 'Completed')
            ->orderBy('end_date', 'ASC')
            ->limit(5)
            ->get();

        return response()->json($top5DelayedProjects);
    }

    /**
     * Get number of projects by phase.
     *
     * @return JsonResponse
     */
    public function getProjectsByPhase(): JsonResponse
    {
        $projectsByPhase = Project::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        return response()->json($projectsByPhase);
    }

    /**
     * Get projects by region.
     *
     * @return JsonResponse
     */
    public function getProjectsByRegion(): JsonResponse
    {
        $projectsByRegion = Project::select('location', DB::raw('count(*) as total'))
            ->groupBy('location')
            ->get();

        return response()->json($projectsByRegion);
    }

    /**
     * Get delayed projects.
     *
     * @return JsonResponse
     */
    public function getDelayedProjects(): JsonResponse
    {
        $delayedProjects = Project::where('status', '!=', 'Completed')
            ->where('end_date', '<', now())
            ->get();

        return response()->json($delayedProjects);
    }

    /**
     * Get upcoming inspections.
     *
     * @return JsonResponse
     */
    public function getUpcomingInspections(): JsonResponse
    {
        $upcomingInspections = InspectionReport::where('inspection_date', '>=', now())
            ->orderBy('inspection_date', 'ASC')
            ->get();

        return response()->json($upcomingInspections);
    }

    /**
     * Store a newly created dashboard metric in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'metric_name' => 'required|string|max:255',
            'metric_value' => 'required|integer',
            'description' => 'nullable|string',
        ]);

        $dashboardMetric = DashboardMetric::create($validatedData);

        return response()->json($dashboardMetric, 201);
    }

    /**
     * Display the specified dashboard metric.
     */
    public function show(DashboardMetric $dashboardMetric): JsonResponse
    {
        return response()->json($dashboardMetric);
    }

    /**
     * Update the specified dashboard metric in storage.
     */
    public function update(Request $request, DashboardMetric $dashboardMetric): JsonResponse
    {
        $validatedData = $request->validate([
            'metric_name' => 'required|string|max:255',
            'metric_value' => 'required|integer',
            'description' => 'nullable|string',
        ]);

        $dashboardMetric->update($validatedData);

        return response()->json($dashboardMetric);
    }

    /**
     * Remove the specified dashboard metric from storage.
     */
    public function destroy(DashboardMetric $dashboardMetric): JsonResponse
    {
        $dashboardMetric->delete();

        return response()->json(null, 204);
    }
}