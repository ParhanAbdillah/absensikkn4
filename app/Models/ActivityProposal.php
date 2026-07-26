<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityProposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_name',
        'date',
        'budget_requested',
        'budget_approved',
        'description',
        'file_path',
        'status',
        'is_posted_to_cash',
        'user_id',
    ];

    protected $casts = [
        'date' => 'date',
        'budget_requested' => 'decimal:2',
        'budget_approved' => 'decimal:2',
        'is_posted_to_cash' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
