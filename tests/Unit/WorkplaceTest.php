<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Workplace;
use App\Models\Trip;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WorkplaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_workplace_can_be_created()
    {
        $workplace = Workplace::create([
            'name' => 'Test Workplace',
            'address' => 'Test Address 123, Zürich',
            'default_distance_km' => 50.5,
            'default_cost_per_km' => 0.70,
            'is_active' => true
        ]);

        $this->assertInstanceOf(Workplace::class, $workplace);
        $this->assertEquals('Test Workplace', $workplace->name);
        $this->assertEquals('Test Address 123, Zürich', $workplace->address);
        $this->assertEquals(50.5, $workplace->default_distance_km);
        $this->assertEquals(0.70, $workplace->default_cost_per_km);
        $this->assertTrue($workplace->is_active);
    }

    public function test_workplace_fillable_attributes()
    {
        $workplace = new Workplace();
        $expected = [
            'name',
            'address',
            'default_distance_km',
            'default_cost_per_km',
            'is_active'
        ];

        $this->assertEquals($expected, $workplace->getFillable());
    }

    public function test_workplace_casts()
    {
        $workplace = Workplace::create([
            'name' => 'Test Workplace',
            'address' => 'Test Address',
            'default_distance_km' => '50.123',
            'default_cost_per_km' => '0.7000',
            'is_active' => '1'
        ]);

        $this->assertIsString($workplace->default_distance_km);
        $this->assertIsString($workplace->default_cost_per_km);
        $this->assertIsBool($workplace->is_active);
        $this->assertEquals(50.12, $workplace->default_distance_km);
        $this->assertEquals(0.7000, $workplace->default_cost_per_km);
        $this->assertTrue($workplace->is_active);
    }

    public function test_workplace_has_many_trips()
    {
        $workplace = Workplace::create([
            'name' => 'Test Workplace',
            'address' => 'Test Address',
            'default_distance_km' => 50,
            'default_cost_per_km' => 0.70,
            'is_active' => true
        ]);

        $trip = Trip::create([
            'workplace_id' => $workplace->id,
            'distance_km' => 45,
            'departure_date' => '2025-01-15',
            'return_date' => '2025-01-17',
            'overnight_days' => 2,
            'cost_per_km' => 0.70,
            'total_cost' => 63.00
        ]);

        $this->assertTrue($workplace->trips()->exists());
        $this->assertEquals(1, $workplace->trips()->count());
        $this->assertEquals($trip->id, $workplace->trips()->first()->id);
    }

    public function test_workplace_active_scope()
    {
        $activeWorkplace = Workplace::create([
            'name' => 'Active Workplace',
            'address' => 'Active Address',
            'default_distance_km' => 50,
            'default_cost_per_km' => 0.70,
            'is_active' => true
        ]);

        $inactiveWorkplace = Workplace::create([
            'name' => 'Inactive Workplace',
            'address' => 'Inactive Address',
            'default_distance_km' => 30,
            'default_cost_per_km' => 0.70,
            'is_active' => false
        ]);

        $activeWorkplaces = Workplace::active()->get();

        $this->assertEquals(1, $activeWorkplaces->count());
        $this->assertEquals($activeWorkplace->id, $activeWorkplaces->first()->id);
        $this->assertNotContains($inactiveWorkplace->id, $activeWorkplaces->pluck('id'));
    }

    public function test_workplace_requires_name()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Workplace::create([
            'address' => 'Test Address',
            'default_distance_km' => 50,
            'default_cost_per_km' => 0.70,
            'is_active' => true
        ]);
    }

    public function test_workplace_defaults()
    {
        $workplace = new Workplace([
            'name' => 'Test Workplace',
            'address' => 'Test Address',
            'default_distance_km' => 50,
            'default_cost_per_km' => 0.70
        ]);

        // Test that is_active defaults to false when not set
        $this->assertNull($workplace->is_active);
    }
}