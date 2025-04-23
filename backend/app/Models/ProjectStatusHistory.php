<?php

namespace App\Models;

// AMHARA-IP-PROJECT/backend/app/Models/ProjectStatusHistory.php

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'status',
        'changed_by',
    ];

    /**
     * Get the project that owns the status history.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the user that changed the status.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}