<?php

namespace App\Models;

// AMHARA-IP-PROJECT/backend/app/Models/Project.php

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_name',
        'contractor_id',
        'consultancy_id',
        'location',
        'start_date',
        'end_date',
        'status',
    ];

    /**
     * Get the contractor that owns the project.
     */
    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class);
    }

    /**
     * Get the consultancy that owns the project.
     */
    public function consultancy(): BelongsTo
    {
        return $this->belongsTo(Consultancy::class);
    }

    /**
     * Get the milestones for the project.
     */
    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class);
    }

    /**
     * Get the status history for the project.
     */
    public function statusHistory(): HasMany
    {
        return $this->hasMany(ProjectStatusHistory::class);
    }

    /**
     * Get the inspection reports for the project.
     */
    public function inspectionReports(): HasMany
    {
        return $this->hasMany(InspectionReport::class);
    }

    /**
     * Get the retention funds for the project.
     */
    public function retentionFunds(): HasMany
    {
        return $this->hasMany(RetentionFund::class);
    }

    /**
     * Get the feedback for the project.
     */
    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }
}