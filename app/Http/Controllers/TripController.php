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
        $trips = Trip::with('workplace')
            ->where('user_id', auth()->id())
            ->orderBy('departure_date', 'desc')
            ->get();
        return view('trips.index', compact('trips'));
    }

    public function create(): View
    {
        $workplaces = Workplace::active()
            ->where('user_id', auth()->id())
            ->orderBy('name')
            ->get();
        $defaultWorkplace = Workplace::where('user_id', auth()->id())
            ->active()
            ->where('is_default', true)
            ->first();
        return view('trips.create', compact('workplaces', 'defaultWorkplace'));
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
        $validated['user_id'] = auth()->id();

        Trip::create($validated);

        return redirect()->route('trips.index')->with('success', 'Fahrt erfolgreich hinzugefügt!');
    }

    public function show(Trip $trip): View
    {
        if ($trip->user_id !== auth()->id()) {
            abort(403);
        }
        
        $trip->load('workplace');
        return view('trips.show', compact('trip'));
    }

    public function edit(Trip $trip): View
    {
        if ($trip->user_id !== auth()->id()) {
            abort(403);
        }
        
        $workplaces = Workplace::active()
            ->where('user_id', auth()->id())
            ->orderBy('name')
            ->get();
        return view('trips.edit', compact('trip', 'workplaces'));
    }

    public function update(Request $request, Trip $trip): RedirectResponse
    {
        if ($trip->user_id !== auth()->id()) {
            abort(403);
        }
        
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
        if ($trip->user_id !== auth()->id()) {
            abort(403);
        }
        
        $trip->delete();
        return redirect()->route('trips.index')->with('success', 'Fahrt erfolgreich gelöscht!');
    }

    public function getWorkplaceData(Workplace $workplace)
    {
        if ($workplace->user_id !== auth()->id()) {
            abort(403);
        }
        
        return response()->json([
            'default_distance_km' => $workplace->default_distance_km,
            'default_cost_per_km' => $workplace->default_cost_per_km
        ]);
    }
}