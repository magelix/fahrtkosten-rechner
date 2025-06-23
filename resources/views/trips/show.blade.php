@extends('layout')

@section('title', 'Fahrt-Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Fahrt-Details</h1>
    <div>
        <a href="{{ route('trips.edit', $trip) }}" class="btn btn-primary">Bearbeiten</a>
        <a href="{{ route('trips.index') }}" class="btn btn-secondary">Zurück zur Liste</a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ $trip->workplace->name }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Adresse:</strong><br>{{ $trip->workplace->address }}</p>
                        <p><strong>Entfernung (einfach):</strong><br>{{ number_format($trip->distance_km, 1) }} km</p>
                        <p><strong>Gesamtentfernung:</strong><br>{{ number_format($trip->distance_km * 2, 1) }} km</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Abreise:</strong><br>{{ $trip->departure_date->format('d.m.Y') }}</p>
                        <p><strong>Rückkehr:</strong><br>{{ $trip->return_date->format('d.m.Y') }}</p>
                        <p><strong>Übernachtungen:</strong><br>{{ $trip->overnight_days }} Nächte</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Kostenberechnung</h5>
            </div>
            <div class="card-body">
                <p><strong>Kosten pro km:</strong><br>{{ number_format($trip->cost_per_km, 2) }} CHF</p>
                <hr>
                <p class="mb-1">{{ number_format($trip->distance_km * 2, 1) }} km × {{ number_format($trip->cost_per_km, 2) }} CHF</p>
                <p class="mb-0"><strong>Gesamtkosten:</strong><br>
                    <span class="h4 text-success">{{ number_format($trip->total_cost, 2) }} CHF</span>
                </p>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Aktionen</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('trips.edit', $trip) }}" class="btn btn-outline-primary">Bearbeiten</a>
                    <form action="{{ route('trips.destroy', $trip) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100" 
                                onclick="return confirm('Fahrt wirklich löschen?')">Löschen</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection