<?php

namespace App\Models;

// AMHARA-IP-PROJECT/backend/app/Models/DashboardMetric.php

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DashboardMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'metric_name',
        'metric_value',
        'description',
    ];
}