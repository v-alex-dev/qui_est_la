<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Visit;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'last_visited_at',
        'badge_id',
    ];

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }
}
