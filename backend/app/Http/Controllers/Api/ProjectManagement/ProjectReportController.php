<?php

namespace App\Http\Controllers\Api\ProjectManagement;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\RetentionFund; // Add this line
use App\Models\InspectionReport; // Add this line
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB; // Add this line

class ProjectReportController extends Controller
{
    /**
     * Generate and return a PDF report for a specific project.
     *
     * @param  \App\Models\Project  $project
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function projectDetailReport(Project $project): Response
    {
        $project->load(['contractor', 'consultancy', 'milestones', 'statusHistory', 'inspectionReports', 'retentionFunds', 'feedbacks']);

        $html = View::make('reports.project_detail', compact('project'))->render();

        $pdf = Pdf::loadHTML($html);

        return $pdf->download('project_detail_report_' . $project->id . '.pdf');
    }

    /**
     * Generate and return a PDF report for project status history.
     *
     * @param  \App\Models\Project  $project
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function projectStatusHistoryReport(Project $project): Response
    {
        $statusHistory = $project->statusHistory()->with('user')->get();

        $html = View::make('reports.project_status_history', compact('project', 'statusHistory'))->render();

        $pdf = Pdf::loadHTML($html);

        return $pdf->download('project_status_history_report_' . $project->id . '.pdf');
    }

    /**
     * Generate and return a PDF report for projects by phase.
     *
     * @param  string  $phase
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function projectsByPhaseReport(string $phase): Response
    {
        $projects = Project::where('status', $phase)->get();

        $html = View::make('reports.projects_by_phase', compact('projects', 'phase'))->render();

        $pdf = Pdf::loadHTML($html);

        return $pdf->download('projects_by_phase_report_' . $phase . '.pdf');
    }

    /**
     * Generate and return a PDF report for retention work.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function retentionWorkReport(): Response
    {
        $retentionFunds = RetentionFund::with('project')->get();

        $html = View::make('reports.retention_work', compact('retentionFunds'))->render();

        $pdf = Pdf::loadHTML($html);

        return $pdf->download('retention_work_report.pdf');
    }

    /**
     * Generate and return a PDF report for final inspections.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function finalInspectionReport(): Response
    {
        $inspectionReports = InspectionReport::with('project')->get();

        $html = View::make('reports.final_inspection', compact('inspectionReports'))->render();

        $pdf = Pdf::loadHTML($html);

        return $pdf->download('final_inspection_report.pdf');
    }

    /**
     * Generate and return a PDF report for dashboard analytics.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dashboardReport(): Response
    {
        $totalProjects = Project::count();
        $projectsByPhase = Project::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();
        $projectsByRegion = Project::select('location', DB::raw('count(*) as total'))
            ->groupBy('location')
            ->get();

        $html = View::make('reports.dashboard', compact('totalProjects', 'projectsByPhase', 'projectsByRegion'))->render();

        $pdf = Pdf::loadHTML($html);

        return $pdf->download('dashboard_report.pdf');
    }
}