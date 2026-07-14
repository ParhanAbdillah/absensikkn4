<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'schedule_id',
        'location_id',
        'check_in_at',
        'check_in_lat',
        'check_in_lng',
        'face_match_score',
        'photo_path',
        'distance_meters',
        'status',
        'notes',
        'approved_by',
    ];

    protected $casts = [
        'check_in_at' => 'datetime',
        'check_in_lat' => 'float',
        'check_in_lng' => 'float',
        'face_match_score' => 'float',
        'distance_meters' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
