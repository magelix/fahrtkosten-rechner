<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function calculateTotalCost(): float
    {
        return $this->distance_km * 2 * $this->cost_per_km;
    }

    public function calculateOvernightDays(): int
    {
        if (!$this->departure_date || !$this->return_date) {
            return 0;
        }
        
        $days = $this->departure_date->diffInDays($this->return_date);
        return max(0, $days);
    }
}
