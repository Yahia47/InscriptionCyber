<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workshop extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'instructor',
        'date',
        'time',
        'location',
        'max_participants'
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i'
    ];

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'registrations')
            ->withTimestamps()
            ->withPivot('registered_at');
    }

    public function getRemainingSeatsAttribute()
    {
        return $this->max_participants - $this->registrations()->count();
    }

    public function isFullyBooked()
    {
        return $this->remaining_seats <= 0;
    }
}
