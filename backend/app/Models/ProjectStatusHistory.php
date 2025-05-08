<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectStatusHistory extends Model  // Ensure name matches the correct Model
{
    use HasFactory;

    protected $table = 'project_status_history';   // this should match the migration name

    protected $fillable = [
        'project_id',
        'status',
        'changed_by',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}