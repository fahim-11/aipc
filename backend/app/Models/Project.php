<?php namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model {
    use HasFactory;
    protected $fillable = [
        'name', 'contractor_name', 'contractor_contact',
        'consultancy_name', 'consultancy_contact', 'location',
        'start_date', 'end_date', 'status', 'phases_milestones_details'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function milestones() {
        return $this->hasMany(Milestone::class);
    }

    public function retentionFund() {
        return $this->hasOne(RetentionFund::class);
    }

    public function inspectionReports() {
        return $this->hasMany(InspectionReport::class);
    }

    // If using foreign keys:
    // public function contractor() {
    //     return $this->belongsTo(Contractor::class);
    // }
    // public function consultancy() {
    //     return $this->belongsTo(Consultancy::class);
    // }
}