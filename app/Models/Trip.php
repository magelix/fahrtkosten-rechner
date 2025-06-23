<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'workplace_id',
        'distance_km',
        'departure_date',
        'return_date',
        'overnight_days',
        'cost_per_km',
        'total_cost'
    ];

    protected $casts = [
        'departure_date' => 'date',
        'return_date' => 'date',
        'distance_km' => 'decimal:2',
        'cost_per_km' => 'decimal:4',
        'total_cost' => 'decimal:2'
    ];

    public function workplace()
    {
        return $this->belongsTo(Workplace::class);
    }

    public function calculateTotalCost(): float
    {
        return $this->distance_km * 2 * $this->cost_per_km;
    }
}
