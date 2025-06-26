@extends('layout')

@section('title', 'Dashboard - Fahrtkosten-Rechner')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Willkommen, {{ Auth::user()->name }}!</h1>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body text-center">
                <h5 class="card-title">Neue Fahrt</h5>
                <p class="card-text">Erfassen Sie eine neue Geschäftsfahrt</p>
                <a href="{{ route('trips.create') }}" class="btn btn-primary">Fahrt erfassen</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body text-center">
                <h5 class="card-title">Alle Fahrten</h5>
                <p class="card-text">Übersicht aller erfassten Fahrten</p>
                <a href="{{ route('trips.index') }}" class="btn btn-outline-primary">Fahrten anzeigen</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body text-center">
                <h5 class="card-title">Arbeitsplätze</h5>
                <p class="card-text">Verwalten Sie Ihre Arbeitsplätze</p>
                <a href="{{ route('workplaces.index') }}" class="btn btn-outline-primary">Arbeitsplätze</a>
            </div>
        </div>
    </div>
</div>

@if(isset($recentTrips) && $recentTrips->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <h3>Letzte Fahrten</h3>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Datum</th>
                        <th>Arbeitsplatz</th>
                        <th>Adresse</th>
                        <th>Kilometer</th>
                        <th>Übernachtungen</th>
                        <th>Kosten</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentTrips as $trip)
                    <tr>
                        <td>{{ $trip->departure_date->format('d.m.Y') }}</td>
                        <td>{{ $trip->workplace->name ?? 'N/A' }}</td>
                        <td>{{ $trip->workplace->address ?? 'N/A' }}</td>
                        <td>{{ $trip->distance_km }} km</td>
                        <td>{{ $trip->overnight_days }} Tage</td>
                        <td>{{ number_format($trip->total_cost, 2) }} CHF</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection