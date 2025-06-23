@extends('layout')

@section('title', 'Arbeitsplatz-Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>{{ $workplace->name }}</h1>
    <div>
        <a href="{{ route('workplaces.edit', $workplace) }}" class="btn btn-primary">Bearbeiten</a>
        <a href="{{ route('workplaces.index') }}" class="btn btn-secondary">Zurück zur Liste</a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Arbeitsplatz-Informationen</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Name:</strong><br>{{ $workplace->name }}</p>
                        <p><strong>Adresse:</strong><br>{{ $workplace->address }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Standard-Entfernung:</strong><br>{{ number_format($workplace->default_distance_km, 1) }} km</p>
                        <p><strong>Standard-Kosten/km:</strong><br>{{ number_format($workplace->default_cost_per_km, 2) }} CHF</p>
                        <p><strong>Status:</strong><br>
                            @if($workplace->is_active)
                                <span class="badge bg-success">Aktiv</span>
                            @else
                                <span class="badge bg-secondary">Inaktiv</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        @if($workplace->trips->isNotEmpty())
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">Fahrten zu diesem Arbeitsplatz ({{ $workplace->trips->count() }})</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Datum</th>
                                <th>Entfernung</th>
                                <th>Kosten/km</th>
                                <th>Gesamtkosten</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($workplace->trips->sortByDesc('departure_date') as $trip)
                            <tr>
                                <td>{{ $trip->departure_date->format('d.m.Y') }} - {{ $trip->return_date->format('d.m.Y') }}</td>
                                <td>{{ number_format($trip->distance_km * 2, 1) }} km</td>
                                <td>{{ number_format($trip->cost_per_km, 2) }} CHF</td>
                                <td><strong>{{ number_format($trip->total_cost, 2) }} CHF</strong></td>
                                <td>
                                    <a href="{{ route('trips.show', $trip) }}" class="btn btn-outline-primary btn-sm">Details</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Statistiken</h6>
            </div>
            <div class="card-body">
                <p><strong>Anzahl Fahrten:</strong><br>{{ $workplace->trips->count() }}</p>
                @if($workplace->trips->isNotEmpty())
                    <p><strong>Gesamtentfernung:</strong><br>{{ number_format($workplace->trips->sum(function($trip) { return $trip->distance_km * 2; }), 1) }} km</p>
                    <p><strong>Gesamtkosten:</strong><br>{{ number_format($workplace->trips->sum('total_cost'), 2) }} CHF</p>
                    <p><strong>Letzte Fahrt:</strong><br>{{ $workplace->trips->sortByDesc('departure_date')->first()->departure_date->format('d.m.Y') }}</p>
                @endif
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Aktionen</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('trips.create', ['workplace' => $workplace->id]) }}" class="btn btn-success">Neue Fahrt hinzufügen</a>
                    <a href="{{ route('workplaces.edit', $workplace) }}" class="btn btn-outline-primary">Bearbeiten</a>
                    <form action="{{ route('workplaces.destroy', $workplace) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100" 
                                onclick="return confirm('Arbeitsplatz wirklich löschen? Alle zugehörigen Fahrten werden ebenfalls gelöscht!')">Löschen</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection