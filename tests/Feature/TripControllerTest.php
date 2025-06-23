<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Trip;
use App\Models\Workplace;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TripControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->activeWorkplace = Workplace::create([
            'name' => 'Active Workplace',
            'address' => 'Active Address',
            'default_distance_km' => 50,
            'default_cost_per_km' => 0.70,
            'is_active' => true
        ]);

        $this->inactiveWorkplace = Workplace::create([
            'name' => 'Inactive Workplace',
            'address' => 'Inactive Address',
            'default_distance_km' => 30,
            'default_cost_per_km' => 0.60,
            'is_active' => false
        ]);
    }

    public function test_trip_index_displays_trips_with_workplaces()
    {
        $trip1 = Trip::create([
            'workplace_id' => $this->activeWorkplace->id,
            'distance_km' => 45,
            'departure_date' => '2025-01-15',
            'return_date' => '2025-01-17',
            'overnight_days' => 2,
            'cost_per_km' => 0.70,
            'total_cost' => 63.00
        ]);

        $trip2 = Trip::create([
            'workplace_id' => $this->activeWorkplace->id,
            'distance_km' => 60,
            'departure_date' => '2025-01-20',
            'return_date' => '2025-01-22',
            'overnight_days' => 2,
            'cost_per_km' => 0.75,
            'total_cost' => 90.00
        ]);

        $response = $this->get(route('trips.index'));

        $response->assertStatus(200);
        $response->assertSee('Active Workplace');
        $response->assertSee('45');
        $response->assertSee('60');
        $response->assertSee('63.00');
        $response->assertSee('90.00');
    }

    public function test_trip_index_orders_by_departure_date_desc()
    {
        $trip1 = Trip::create([
            'workplace_id' => $this->activeWorkplace->id,
            'distance_km' => 45,
            'departure_date' => '2025-01-10',
            'return_date' => '2025-01-12',
            'overnight_days' => 2,
            'cost_per_km' => 0.70,
            'total_cost' => 63.00
        ]);

        $trip2 = Trip::create([
            'workplace_id' => $this->activeWorkplace->id,
            'distance_km' => 60,
            'departure_date' => '2025-01-20',
            'return_date' => '2025-01-22',
            'overnight_days' => 2,
            'cost_per_km' => 0.75,
            'total_cost' => 90.00
        ]);

        $trips = Trip::with('workplace')->orderBy('departure_date', 'desc')->get();
        
        $this->assertEquals('2025-01-20', $trips->first()->departure_date->format('Y-m-d'));
        $this->assertEquals('2025-01-10', $trips->last()->departure_date->format('Y-m-d'));
    }

    public function test_trip_create_displays_form_with_active_workplaces()
    {
        $response = $this->get(route('trips.create'));

        $response->assertStatus(200);
        $response->assertSee('Active Workplace');
        $response->assertDontSee('Inactive Workplace');
        $response->assertSee('name="workplace_id"', false);
        $response->assertSee('name="distance_km"', false);
        $response->assertSee('name="departure_date"', false);
        $response->assertSee('name="return_date"', false);
    }

    public function test_trip_can_be_stored()
    {
        $data = [
            'workplace_id' => $this->activeWorkplace->id,
            'distance_km' => 42.5,
            'departure_date' => '2025-02-15',
            'return_date' => '2025-02-17',
            'overnight_days' => 2,
            'cost_per_km' => 0.75
        ];

        $response = $this->post(route('trips.store'), $data);

        $response->assertRedirect(route('trips.index'));
        $response->assertSessionHas('success', 'Fahrt erfolgreich hinzugefügt!');

        $expectedTotalCost = 42.5 * 2 * 0.75; // 63.75
        
        $this->assertDatabaseHas('trips', [
            'workplace_id' => $this->activeWorkplace->id,
            'distance_km' => 42.5,
            'departure_date' => '2025-02-15',
            'return_date' => '2025-02-17',
            'overnight_days' => 2,
            'cost_per_km' => 0.75,
            'total_cost' => $expectedTotalCost
        ]);
    }

    public function test_trip_store_calculates_total_cost()
    {
        $data = [
            'workplace_id' => $this->activeWorkplace->id,
            'distance_km' => 50,
            'departure_date' => '2025-02-15',
            'return_date' => '2025-02-17',
            'overnight_days' => 2,
            'cost_per_km' => 0.80
        ];

        $response = $this->post(route('trips.store'), $data);

        $response->assertRedirect(route('trips.index'));
        
        $trip = Trip::first();
        $expectedTotalCost = 50 * 2 * 0.80; // 80.00
        $this->assertEquals($expectedTotalCost, $trip->total_cost);
    }

    public function test_trip_store_validation_fails_with_invalid_data()
    {
        $data = [
            'workplace_id' => 999, // Non-existent workplace
            'distance_km' => -5,
            'departure_date' => 'invalid-date',
            'return_date' => '2025-01-10',
            'overnight_days' => -1,
            'cost_per_km' => -0.5
        ];

        $response = $this->post(route('trips.store'), $data);

        $response->assertSessionHasErrors([
            'workplace_id',
            'distance_km',
            'departure_date',
            'overnight_days',
            'cost_per_km'
        ]);
    }

    public function test_trip_store_validates_return_date_after_departure()
    {
        $data = [
            'workplace_id' => $this->activeWorkplace->id,
            'distance_km' => 50,
            'departure_date' => '2025-02-15',
            'return_date' => '2025-02-10', // Before departure date
            'overnight_days' => 2,
            'cost_per_km' => 0.70
        ];

        $response = $this->post(route('trips.store'), $data);

        $response->assertSessionHasErrors(['return_date']);
    }

    public function test_trip_show_displays_trip_with_workplace()
    {
        $trip = Trip::create([
            'workplace_id' => $this->activeWorkplace->id,
            'distance_km' => 45,
            'departure_date' => '2025-01-15',
            'return_date' => '2025-01-17',
            'overnight_days' => 2,
            'cost_per_km' => 0.70,
            'total_cost' => 63.00
        ]);

        $response = $this->get(route('trips.show', $trip));

        $response->assertStatus(200);
        $response->assertSee('Active Workplace');
        $response->assertSee('45');
        $response->assertSee('15.01.2025');
        $response->assertSee('17.01.2025');
        $response->assertSee('63.00');
    }

    public function test_trip_edit_displays_form_with_data()
    {
        $trip = Trip::create([
            'workplace_id' => $this->activeWorkplace->id,
            'distance_km' => 45,
            'departure_date' => '2025-01-15',
            'return_date' => '2025-01-17',
            'overnight_days' => 2,
            'cost_per_km' => 0.70,
            'total_cost' => 63.00
        ]);

        $response = $this->get(route('trips.edit', $trip));

        $response->assertStatus(200);
        $response->assertSee('Active Workplace');
        $response->assertSee('45');
        $response->assertSee('2025-01-15');
        $response->assertSee('2025-01-17');
        $response->assertSee('0.70');
    }

    public function test_trip_can_be_updated()
    {
        $trip = Trip::create([
            'workplace_id' => $this->activeWorkplace->id,
            'distance_km' => 45,
            'departure_date' => '2025-01-15',
            'return_date' => '2025-01-17',
            'overnight_days' => 2,
            'cost_per_km' => 0.70,
            'total_cost' => 63.00
        ]);

        $data = [
            'workplace_id' => $this->activeWorkplace->id,
            'distance_km' => 55,
            'departure_date' => '2025-02-20',
            'return_date' => '2025-02-22',
            'overnight_days' => 3,
            'cost_per_km' => 0.80
        ];

        $response = $this->put(route('trips.update', $trip), $data);

        $response->assertRedirect(route('trips.index'));
        $response->assertSessionHas('success', 'Fahrt erfolgreich aktualisiert!');

        $trip->refresh();
        $expectedTotalCost = 55 * 2 * 0.80; // 88.00
        
        $this->assertEquals(55, $trip->distance_km);
        $this->assertEquals('2025-02-20', $trip->departure_date->format('Y-m-d'));
        $this->assertEquals('2025-02-22', $trip->return_date->format('Y-m-d'));
        $this->assertEquals(3, $trip->overnight_days);
        $this->assertEquals(0.80, $trip->cost_per_km);
        $this->assertEquals($expectedTotalCost, $trip->total_cost);
    }

    public function test_trip_can_be_deleted()
    {
        $trip = Trip::create([
            'workplace_id' => $this->activeWorkplace->id,
            'distance_km' => 45,
            'departure_date' => '2025-01-15',
            'return_date' => '2025-01-17',
            'overnight_days' => 2,
            'cost_per_km' => 0.70,
            'total_cost' => 63.00
        ]);

        $response = $this->delete(route('trips.destroy', $trip));

        $response->assertRedirect(route('trips.index'));
        $response->assertSessionHas('success', 'Fahrt erfolgreich gelöscht!');

        $this->assertDatabaseMissing('trips', [
            'id' => $trip->id
        ]);
    }

    public function test_get_workplace_data_returns_json()
    {
        $response = $this->get(route('api.workplace.data', $this->activeWorkplace));

        $response->assertStatus(200);
        $response->assertJson([
            'default_distance_km' => 50,
            'default_cost_per_km' => 0.70
        ]);
    }

    public function test_trip_same_day_return_allowed()
    {
        $data = [
            'workplace_id' => $this->activeWorkplace->id,
            'distance_km' => 25,
            'departure_date' => '2025-02-15',
            'return_date' => '2025-02-15', // Same day
            'overnight_days' => 0,
            'cost_per_km' => 0.70
        ];

        $response = $this->post(route('trips.store'), $data);

        $response->assertRedirect(route('trips.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('trips', [
            'departure_date' => '2025-02-15',
            'return_date' => '2025-02-15',
            'overnight_days' => 0
        ]);
    }
}