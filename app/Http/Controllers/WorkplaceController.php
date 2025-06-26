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
        $workplaces = Workplace::where('user_id', auth()->id())
            ->orderBy('name')
            ->get();
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
        $validated['user_id'] = auth()->id();

        Workplace::create($validated);

        return redirect()->route('workplaces.index')->with('success', 'Arbeitsplatz erfolgreich hinzugefügt!');
    }

    public function show(Workplace $workplace): View
    {
        if ($workplace->user_id !== auth()->id()) {
            abort(403);
        }
        
        $workplace->load(['trips' => function($query) {
            $query->where('user_id', auth()->id());
        }]);
        return view('workplaces.show', compact('workplace'));
    }

    public function edit(Workplace $workplace): View
    {
        if ($workplace->user_id !== auth()->id()) {
            abort(403);
        }
        
        return view('workplaces.edit', compact('workplace'));
    }

    public function update(Request $request, Workplace $workplace): RedirectResponse
    {
        if ($workplace->user_id !== auth()->id()) {
            abort(403);
        }
        
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
        if ($workplace->user_id !== auth()->id()) {
            abort(403);
        }
        
        $workplace->delete();
        return redirect()->route('workplaces.index')->with('success', 'Arbeitsplatz erfolgreich gelöscht!');
    }

    public function setDefault(Workplace $workplace): RedirectResponse
    {
        if ($workplace->user_id !== auth()->id()) {
            abort(403);
        }
        
        // Remove default status from all other workplaces for this user
        Workplace::where('user_id', auth()->id())
            ->where('is_default', true)
            ->update(['is_default' => false]);
        
        // Set this workplace as default
        $workplace->update(['is_default' => true]);

        return redirect()->route('workplaces.index')->with('success', 'Standard-Arbeitsplatz erfolgreich gesetzt!');
    }
}
