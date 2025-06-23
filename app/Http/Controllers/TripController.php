<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Workplace;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TripController extends Controller
{
    public function index(): View
    {
        $trips = Trip::with('workplace')->orderBy('departure_date', 'desc')->get();
        return view('trips.index', compact('trips'));
    }

    public function create(): View
    {
        $workplaces = Workplace::active()->orderBy('name')->get();
        return view('trips.create', compact('workplaces'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'workplace_id' => 'required|exists:workplaces,id',
            'distance_km' => 'required|numeric|min:0',
            'departure_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:departure_date',
            'overnight_days' => 'required|integer|min:0',
            'cost_per_km' => 'required|numeric|min:0'
        ]);

        $validated['total_cost'] = $validated['distance_km'] * 2 * $validated['cost_per_km'];

        Trip::create($validated);

        return redirect()->route('trips.index')->with('success', 'Fahrt erfolgreich hinzugefügt!');
    }

    public function show(Trip $trip): View
    {
        $trip->load('workplace');
        return view('trips.show', compact('trip'));
    }

    public function edit(Trip $trip): View
    {
        $workplaces = Workplace::active()->orderBy('name')->get();
        return view('trips.edit', compact('trip', 'workplaces'));
    }

    public function update(Request $request, Trip $trip): RedirectResponse
    {
        $validated = $request->validate([
            'workplace_id' => 'required|exists:workplaces,id',
            'distance_km' => 'required|numeric|min:0',
            'departure_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:departure_date',
            'overnight_days' => 'required|integer|min:0',
            'cost_per_km' => 'required|numeric|min:0'
        ]);

        $validated['total_cost'] = $validated['distance_km'] * 2 * $validated['cost_per_km'];

        $trip->update($validated);

        return redirect()->route('trips.index')->with('success', 'Fahrt erfolgreich aktualisiert!');
    }

    public function destroy(Trip $trip): RedirectResponse
    {
        $trip->delete();
        return redirect()->route('trips.index')->with('success', 'Fahrt erfolgreich gelöscht!');
    }

    public function getWorkplaceData(Workplace $workplace)
    {
        return response()->json([
            'default_distance_km' => $workplace->default_distance_km,
            'default_cost_per_km' => $workplace->default_cost_per_km
        ]);
    }
}