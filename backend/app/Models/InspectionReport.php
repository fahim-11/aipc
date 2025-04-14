<?php namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage; // Added

class InspectionReport extends Model {
    use HasFactory;
    protected $fillable = ['project_id', 'inspection_date', 'inspector_name', 'findings', 'report_file_path', 'original_file_name'];
    protected $casts = ['inspection_date' => 'date'];
    protected $appends = ['report_file_url']; // Added

    public function project() {
        return $this->belongsTo(Project::class);
    }

    // Accessor for file URL
    public function getReportFileUrlAttribute() {
        if ($this->report_file_path) {
            // Assumes 'public' disk and storage:link has been run
            return asset('storage/' . $this->report_file_path);
        }
        return null;
    }
}