<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model {
    use HasFactory;

    protected $fillable = ['project_name', 'complaint_type', 'description', 'contact_info', 'status'];

    // If linking to project:
    // protected $fillable = ['project_id', 'complaint_type', 'description', 'contact_info', 'status'];
    // public function project() {
    //     return $this->belongsTo(Project::class);
    // }
}