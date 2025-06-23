<?php

namespace App\Http\Controllers;

use App\Models\Workplace;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class WorkplaceController extends Controller
{
    public function index(): View
    {
        $workplaces = Workplace::orderBy('name')->get();
        return view('workplaces.index', compact('workplaces'));
    }

    public function create(): View
    {
        return view('workplaces.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'default_distance_km' => 'required|numeric|min:0',
            'default_cost_per_km' => 'required|numeric|min:0'
        ]);

        $validated['is_active'] = $request->has('is_active');

        Workplace::create($validated);

        return redirect()->route('workplaces.index')->with('success', 'Arbeitsplatz erfolgreich hinzugefügt!');
    }

    public function show(Workplace $workplace): View
    {
        $workplace->load('trips');
        return view('workplaces.show', compact('workplace'));
    }

    public function edit(Workplace $workplace): View
    {
        return view('workplaces.edit', compact('workplace'));
    }

    public function update(Request $request, Workplace $workplace): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'default_distance_km' => 'required|numeric|min:0',
            'default_cost_per_km' => 'required|numeric|min:0'
        ]);

        $validated['is_active'] = $request->has('is_active');

        $workplace->update($validated);

        return redirect()->route('workplaces.index')->with('success', 'Arbeitsplatz erfolgreich aktualisiert!');
    }

    public function destroy(Workplace $workplace): RedirectResponse
    {
        $workplace->delete();
        return redirect()->route('workplaces.index')->with('success', 'Arbeitsplatz erfolgreich gelöscht!');
    }
}
