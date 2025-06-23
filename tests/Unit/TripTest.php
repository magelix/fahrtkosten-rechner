<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Trip;
use App\Models\Workplace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class TripTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->workplace = Workplace::create([
            'name' => 'Test Workplace',
            'address' => 'Test Address 123, Zürich',
            'default_distance_km' => 50,
            'default_cost_per_km' => 0.70,
            'is_active' => true
        ]);
    }

    public function test_trip_can_be_created()
    {
        $trip = Trip::create([
            'workplace_id' => $this->workplace->id,
            'distance_km' => 45.5,
            'departure_date' => '2025-01-15',
            'return_date' => '2025-01-17',
            'overnight_days' => 2,
            'cost_per_km' => 0.75,
            'total_cost' => 68.25
        ]);

        $this->assertInstanceOf(Trip::class, $trip);
        $this->assertEquals($this->workplace->id, $trip->workplace_id);
        $this->assertEquals(45.5, $trip->distance_km);
        $this->assertEquals('2025-01-15', $trip->departure_date->format('Y-m-d'));
        $this->assertEquals('2025-01-17', $trip->return_date->format('Y-m-d'));
        $this->assertEquals(2, $trip->overnight_days);
        $this->assertEquals(0.75, $trip->cost_per_km);
        $this->assertEquals(68.25, $trip->total_cost);
    }

    public function test_trip_fillable_attributes()
    {
        $trip = new Trip();
        $expected = [
            'workplace_id',
            'distance_km',
            'departure_date',
            'return_date',
            'overnight_days',
            'cost_per_km',
            'total_cost'
        ];

        $this->assertEquals($expected, $trip->getFillable());
    }

    public function test_trip_casts()
    {
        $trip = Trip::create([
            'workplace_id' => $this->workplace->id,
            'distance_km' => '45.123',
            'departure_date' => '2025-01-15',
            'return_date' => '2025-01-17',
            'overnight_days' => 2,
            'cost_per_km' => '0.7500',
            'total_cost' => '67.685'
        ]);

        $this->assertInstanceOf(Carbon::class, $trip->departure_date);
        $this->assertInstanceOf(Carbon::class, $trip->return_date);
        $this->assertIsString($trip->distance_km);
        $this->assertIsString($trip->cost_per_km);
        $this->assertIsString($trip->total_cost);
        $this->assertEquals(45.12, $trip->distance_km);
        $this->assertEquals(0.7500, $trip->cost_per_km);
        $this->assertEquals(67.69, $trip->total_cost);
    }

    public function test_trip_belongs_to_workplace()
    {
        $trip = Trip::create([
            'workplace_id' => $this->workplace->id,
            'distance_km' => 50,
            'departure_date' => '2025-01-15',
            'return_date' => '2025-01-17',
            'overnight_days' => 2,
            'cost_per_km' => 0.70,
            'total_cost' => 70.00
        ]);

        $this->assertInstanceOf(Workplace::class, $trip->workplace);
        $this->assertEquals($this->workplace->id, $trip->workplace->id);
        $this->assertEquals('Test Workplace', $trip->workplace->name);
    }

    public function test_calculate_total_cost_method()
    {
        $trip = new Trip([
            'distance_km' => 50,
            'cost_per_km' => 0.70
        ]);

        $expectedCost = 50 * 2 * 0.70; // distance × 2 (round trip) × cost per km
        $this->assertEquals($expectedCost, $trip->calculateTotalCost());
        $this->assertEquals(70.0, $trip->calculateTotalCost());
    }

    public function test_calculate_total_cost_with_decimals()
    {
        $trip = new Trip([
            'distance_km' => 42.5,
            'cost_per_km' => 0.75
        ]);

        $expectedCost = 42.5 * 2 * 0.75;
        $this->assertEquals($expectedCost, $trip->calculateTotalCost());
        $this->assertEquals(63.75, $trip->calculateTotalCost());
    }

    public function test_trip_requires_workplace_id()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Trip::create([
            'distance_km' => 50,
            'departure_date' => '2025-01-15',
            'return_date' => '2025-01-17',
            'overnight_days' => 2,
            'cost_per_km' => 0.70,
            'total_cost' => 70.00
        ]);
    }

    public function test_trip_dates_are_carbon_instances()
    {
        $trip = Trip::create([
            'workplace_id' => $this->workplace->id,
            'distance_km' => 50,
            'departure_date' => '2025-01-15',
            'return_date' => '2025-01-17',
            'overnight_days' => 2,
            'cost_per_km' => 0.70,
            'total_cost' => 70.00
        ]);

        $this->assertInstanceOf(Carbon::class, $trip->departure_date);
        $this->assertInstanceOf(Carbon::class, $trip->return_date);
        $this->assertEquals('2025-01-15', $trip->departure_date->format('Y-m-d'));
        $this->assertEquals('2025-01-17', $trip->return_date->format('Y-m-d'));
    }

    public function test_trip_with_same_day_return()
    {
        $trip = Trip::create([
            'workplace_id' => $this->workplace->id,
            'distance_km' => 25,
            'departure_date' => '2025-01-15',
            'return_date' => '2025-01-15',
            'overnight_days' => 0,
            'cost_per_km' => 0.70,
            'total_cost' => 35.00
        ]);

        $this->assertEquals(0, $trip->overnight_days);
        $this->assertEquals($trip->departure_date->format('Y-m-d'), $trip->return_date->format('Y-m-d'));
        $this->assertEquals(35.0, $trip->calculateTotalCost());
    }
}