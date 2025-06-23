@extends('layout')

@section('title', 'Alle Fahrten')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Alle Fahrten</h1>
    <a href="{{ route('trips.create') }}" class="btn btn-primary">Neue Fahrt hinzufügen</a>
</div>

@if($trips->isEmpty())
    <div class="alert alert-info">
        Noch keine Fahrten erfasst. <a href="{{ route('trips.create') }}">Erste Fahrt hinzufügen</a>
    </div>
@else
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Arbeitsplatz</th>
                    <th>Entfernung (km)</th>
                    <th>Abreise</th>
                    <th>Rückkehr</th>
                    <th>Übernachtungen</th>
                    <th>Kosten/km</th>
                    <th>Gesamtkosten</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                @foreach($trips as $trip)
                <tr>
                    <td>
                        <strong>{{ $trip->workplace->name }}</strong><br>
                        <small class="text-muted">{{ $trip->workplace->address }}</small>
                    </td>
                    <td>{{ number_format($trip->distance_km, 1) }}</td>
                    <td>{{ $trip->departure_date->format('d.m.Y') }}</td>
                    <td>{{ $trip->return_date->format('d.m.Y') }}</td>
                    <td>{{ $trip->overnight_days }}</td>
                    <td>{{ number_format($trip->cost_per_km, 2) }} CHF</td>
                    <td><strong>{{ number_format($trip->total_cost, 2) }} CHF</strong></td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('trips.show', $trip) }}" class="btn btn-outline-primary">Details</a>
                            <a href="{{ route('trips.edit', $trip) }}" class="btn btn-outline-secondary">Bearbeiten</a>
                            <form action="{{ route('trips.destroy', $trip) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Fahrt wirklich löschen?')">Löschen</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Zusammenfassung</h5>
                    <p class="card-text">
                        <strong>Anzahl Fahrten:</strong> {{ $trips->count() }}<br>
                        <strong>Gesamtentfernung:</strong> {{ number_format($trips->sum('distance_km') * 2, 1) }} km<br>
                        <strong>Gesamtkosten:</strong> {{ number_format($trips->sum('total_cost'), 2) }} CHF
                    </p>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection