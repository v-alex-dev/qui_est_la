<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Training;
use App\Models\Visit;

class StaffMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'function',
        'room',
        'phone',
    ];

    public function trainings()
    {
        return $this->hasMany(Training::class);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }
} 