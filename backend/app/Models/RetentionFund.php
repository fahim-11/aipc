<?php namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetentionFund extends Model {
    use HasFactory;
    protected $fillable = ['project_id', 'amount_held', 'release_conditions', 'status', 'release_date'];
    protected $casts = ['release_date' => 'date', 'amount_held' => 'decimal:2'];

    public function project() {
        return $this->belongsTo(Project::class);
    }
}