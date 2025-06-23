<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Workplace;
use App\Models\Trip;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WorkplaceControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_workplace_index_displays_workplaces()
    {
        $workplace1 = Workplace::create([
            'name' => 'Workplace A',
            'address' => 'Address A',
            'default_distance_km' => 30,
            'default_cost_per_km' => 0.70,
            'is_active' => true
        ]);

        $workplace2 = Workplace::create([
            'name' => 'Workplace B',
            'address' => 'Address B',
            'default_distance_km' => 50,
            'default_cost_per_km' => 0.70,
            'is_active' => false
        ]);

        $response = $this->get(route('workplaces.index'));

        $response->assertStatus(200);
        $response->assertSee('Workplace A');
        $response->assertSee('Workplace B');
        $response->assertSee('Address A');
        $response->assertSee('Address B');
    }

    public function test_workplace_create_displays_form()
    {
        $response = $this->get(route('workplaces.create'));

        $response->assertStatus(200);
        $response->assertSee('name="name"', false);
        $response->assertSee('name="address"', false);
        $response->assertSee('name="default_distance_km"', false);
        $response->assertSee('name="default_cost_per_km"', false);
    }

    public function test_workplace_can_be_stored()
    {
        $data = [
            'name' => 'New Workplace',
            'address' => 'New Address 123',
            'default_distance_km' => 45,
            'default_cost_per_km' => 0.75,
            'is_active' => '1'
        ];

        $response = $this->post(route('workplaces.store'), $data);

        $response->assertRedirect(route('workplaces.index'));
        $response->assertSessionHas('success', 'Arbeitsplatz erfolgreich hinzugefügt!');

        $this->assertDatabaseHas('workplaces', [
            'name' => 'New Workplace',
            'address' => 'New Address 123',
            'default_distance_km' => 45,
            'default_cost_per_km' => 0.75,
            'is_active' => true
        ]);
    }

    public function test_workplace_store_without_is_active_checkbox()
    {
        $data = [
            'name' => 'New Workplace',
            'address' => 'New Address 123',
            'default_distance_km' => 45,
            'default_cost_per_km' => 0.75
        ];

        $response = $this->post(route('workplaces.store'), $data);

        $response->assertRedirect(route('workplaces.index'));
        
        $this->assertDatabaseHas('workplaces', [
            'name' => 'New Workplace',
            'is_active' => false
        ]);
    }

    public function test_workplace_store_validation_fails_with_invalid_data()
    {
        $data = [
            'name' => '',
            'address' => '',
            'default_distance_km' => -5,
            'default_cost_per_km' => -1
        ];

        $response = $this->post(route('workplaces.store'), $data);

        $response->assertSessionHasErrors([
            'name',
            'address', 
            'default_distance_km',
            'default_cost_per_km'
        ]);
    }

    public function test_workplace_show_displays_workplace_with_trips()
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

        $response = $this->get(route('workplaces.show', $workplace));

        $response->assertStatus(200);
        $response->assertSee('Test Workplace');
        $response->assertSee('Test Address');
        $response->assertSee('50');
        $response->assertSee('0.70');
    }

    public function test_workplace_edit_displays_form_with_data()
    {
        $workplace = Workplace::create([
            'name' => 'Test Workplace',
            'address' => 'Test Address',
            'default_distance_km' => 50,
            'default_cost_per_km' => 0.70,
            'is_active' => true
        ]);

        $response = $this->get(route('workplaces.edit', $workplace));

        $response->assertStatus(200);
        $response->assertSee('Test Workplace');
        $response->assertSee('Test Address');
        $response->assertSee('50');
        $response->assertSee('0.70');
    }

    public function test_workplace_can_be_updated()
    {
        $workplace = Workplace::create([
            'name' => 'Original Name',
            'address' => 'Original Address',
            'default_distance_km' => 30,
            'default_cost_per_km' => 0.60,
            'is_active' => false
        ]);

        $data = [
            'name' => 'Updated Name',
            'address' => 'Updated Address',
            'default_distance_km' => 55,
            'default_cost_per_km' => 0.80,
            'is_active' => '1'
        ];

        $response = $this->put(route('workplaces.update', $workplace), $data);

        $response->assertRedirect(route('workplaces.index'));
        $response->assertSessionHas('success', 'Arbeitsplatz erfolgreich aktualisiert!');

        $workplace->refresh();
        $this->assertEquals('Updated Name', $workplace->name);
        $this->assertEquals('Updated Address', $workplace->address);
        $this->assertEquals(55, $workplace->default_distance_km);
        $this->assertEquals(0.80, $workplace->default_cost_per_km);
        $this->assertTrue($workplace->is_active);
    }

    public function test_workplace_update_validation_fails_with_invalid_data()
    {
        $workplace = Workplace::create([
            'name' => 'Test Workplace',
            'address' => 'Test Address',
            'default_distance_km' => 50,
            'default_cost_per_km' => 0.70,
            'is_active' => true
        ]);

        $data = [
            'name' => '',
            'address' => '',
            'default_distance_km' => -10,
            'default_cost_per_km' => -0.5
        ];

        $response = $this->put(route('workplaces.update', $workplace), $data);

        $response->assertSessionHasErrors([
            'name',
            'address',
            'default_distance_km',
            'default_cost_per_km'
        ]);
    }

    public function test_workplace_can_be_deleted()
    {
        $workplace = Workplace::create([
            'name' => 'Test Workplace',
            'address' => 'Test Address',
            'default_distance_km' => 50,
            'default_cost_per_km' => 0.70,
            'is_active' => true
        ]);

        $response = $this->delete(route('workplaces.destroy', $workplace));

        $response->assertRedirect(route('workplaces.index'));
        $response->assertSessionHas('success', 'Arbeitsplatz erfolgreich gelöscht!');

        $this->assertDatabaseMissing('workplaces', [
            'id' => $workplace->id
        ]);
    }

    public function test_workplace_index_orders_by_name()
    {
        Workplace::create([
            'name' => 'Z Workplace',
            'address' => 'Address Z',
            'default_distance_km' => 30,
            'default_cost_per_km' => 0.70,
            'is_active' => true
        ]);

        Workplace::create([
            'name' => 'A Workplace',
            'address' => 'Address A',
            'default_distance_km' => 50,
            'default_cost_per_km' => 0.70,
            'is_active' => true
        ]);

        $response = $this->get(route('workplaces.index'));
        
        $response->assertStatus(200);
        
        // Check that content appears in alphabetical order
        $content = $response->getContent();
        $positionA = strpos($content, 'A Workplace');
        $positionZ = strpos($content, 'Z Workplace');
        
        $this->assertLessThan($positionZ, $positionA);
    }
}