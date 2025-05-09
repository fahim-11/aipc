<?php

namespace App\Models;

// AMHARA-IP-PROJECT/backend/app/Models/InspectionReport.php

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspectionReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'inspection_date',
        'team_inspector_name',
        'findings',
        'supporting_files',
    ];

    /**
     * Get the project that owns the inspection report.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}