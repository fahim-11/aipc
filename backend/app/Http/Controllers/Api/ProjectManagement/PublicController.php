<?php
namespace App\Http\Controllers\Api\ProjectManagement;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function publicProjects(Request $request)
    {
        $query = Project::query();

        // Basic Search Example
        if ($request->has('search') && $request->search != '') {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('contractor_name', 'like', $searchTerm)
                  ->orWhere('consultancy_name', 'like', $searchTerm)
                  ->orWhere('location', 'like', $searchTerm);
            });
        }

        // Select only public fields and rename 'name' to 'title'
        $projects = $query->select([
                'id',
                'name as title', // Rename name to title
                'status',
                'contractor_name', // Directly use names stored
                'consultancy_name',
                'start_date',
                'end_date',
                'location' // Added location as it's often public info
            ])
            ->orderBy('start_date', 'desc')
            ->paginate(10); // Add pagination

        return response()->json($projects);
    }
}