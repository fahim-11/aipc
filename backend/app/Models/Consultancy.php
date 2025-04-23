<?php

namespace App\Models;

// AMHARA-IP-PROJECT/backend/app/Models/Consultancy.php

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consultancy extends Model
{
    use HasFactory;

    protected $fillable = [
        'consultancy_name',
        'phone_number',
        'email_address',
        'company_address',
    ];

    /**
     * Get the projects for the consultancy.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}