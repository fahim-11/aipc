<?php

namespace App\Models;

// AMHARA-IP-PROJECT/backend/app/Models/Feedback.php

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'complaint_type',
        'description',
        'contact_email',
        'status', // Added status field
    ];

    protected $attributes = [
        'status' => 'new', // Default status is 'new'
    ];
}