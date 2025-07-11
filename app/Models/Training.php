<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Visit;

class Training extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'date',
        'room',
        'staff_member_id',
    ];

    public function staffMember()
    {
        return $this->belongsTo(StaffMember::class);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }
} 