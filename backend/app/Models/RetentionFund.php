<?php

namespace App\Models;

// AMHARA-IP-PROJECT/backend/app/Models/RetentionFund.php

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RetentionFund extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'amount_held',
        'conditions_for_release',
        'actual_release_status',
        'withheld_date',
        'released_date',
    ];

    /**
     * Get the project that owns the retention fund.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}