<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitor_id',
        'staff_member_id',
        'training_id',
        'purpose',
        'entered_at',
        'exited_at',
        'badge_id',
    ];

    public function visitor()
    {
        return $this->belongsTo(Visitor::class);
    }

    public function staffMember()
    {
        return $this->belongsTo(StaffMember::class);
    }

    public function training()
    {
        return $this->belongsTo(Training::class);
    }
} 