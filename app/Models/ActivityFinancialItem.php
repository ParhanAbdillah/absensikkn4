<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityFinancialItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_financial_report_id',
        'type',
        'description',
        'qty',
        'price',
        'total',
    ];

    protected $casts = [
        'qty' => 'integer',
        'price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(ActivityFinancialReport::class, 'activity_financial_report_id');
    }
}
