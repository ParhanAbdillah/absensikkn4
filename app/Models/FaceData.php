<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaceData extends Model
{
    use HasFactory;

    protected $table = 'face_data';

    protected $fillable = [
        'user_id',
        'descriptor',
        'reference_photo',
    ];

    protected $casts = [
        'descriptor' => 'array', // Menyimpan array 128 dimensi
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
