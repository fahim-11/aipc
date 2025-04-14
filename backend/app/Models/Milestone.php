<?php namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Milestone extends Model {
    use HasFactory;
    protected $fillable = ['project_id', 'name', 'description', 'target_date', 'status'];
    protected $casts = ['target_date' => 'date'];

    public function project() {
        return $this->belongsTo(Project::class);
    }
}