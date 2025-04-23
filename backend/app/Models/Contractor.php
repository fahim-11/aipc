<?php

namespace App\Models;

// AMHARA-IP-PROJECT/backend/app/Models/Contractor.php

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class Contractor extends Model
{
    use HasFactory;

    protected $fillable = [
        'contractor_name',
        'phone_number',
        'email_address',
        'company_address',
        'unique_id',
    ];

    /**
     * Get the projects for the contractor.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }
}