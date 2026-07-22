<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityReport extends Model
{
    protected $fillable = [
        'user_id',
        'tanggal',
        'nama_kegiatan',
        'deadline',
        'person_in_charge',
        'pic',
        'status',
        'notes',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'deadline' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
