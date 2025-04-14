<?php namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contractor extends Model { // Or Consultancy
    use HasFactory;
    protected $fillable = ['name', 'contact_info'];

    // If using foreign keys in Project model:
    // public function projects() {
    //     return $this->hasMany(Project::class);
    // }
}