<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Workplace extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'address',
        'default_distance_km',
        'default_cost_per_km',
        'is_active',
        'is_default'
    ];

    protected $casts = [
        'default_distance_km' => 'decimal:2',
        'default_cost_per_km' => 'decimal:4',
        'is_active' => 'boolean',
        'is_default' => 'boolean'
    ];

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public static function getDefault()
    {
        return static::active()->default()->first();
    }
}
