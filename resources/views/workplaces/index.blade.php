@extends('layout')

@section('title', 'Arbeitsplätze')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Arbeitsplätze</h1>
    <a href="{{ route('workplaces.create') }}" class="btn btn-primary">Neuen Arbeitsplatz hinzufügen</a>
</div>

@if($workplaces->isEmpty())
    <div class="alert alert-info">
        Noch keine Arbeitsplätze erfasst. <a href="{{ route('workplaces.create') }}">Ersten Arbeitsplatz hinzufügen</a>
    </div>
@else
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Adresse</th>
                    <th>Standard-Entfernung</th>
                    <th>Standard-Kosten/km</th>
                    <th>Status</th>
                    <th>Standard</th>
                    <th>Anzahl Fahrten</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                @foreach($workplaces as $workplace)
                <tr>
                    <td>
                        <strong>{{ $workplace->name }}</strong>
                    </td>
                    <td>{{ $workplace->address }}</td>
                    <td>{{ number_format($workplace->default_distance_km, 1) }} km</td>
                    <td>{{ number_format($workplace->default_cost_per_km, 2) }} CHF</td>
                    <td>
                        @if($workplace->is_active)
                            <span class="badge bg-success">Aktiv</span>
                        @else
                            <span class="badge bg-secondary">Inaktiv</span>
                        @endif
                    </td>
                    <td>
                        @if($workplace->is_default)
                            <span class="badge bg-primary">Standard</span>
                        @else
                            <form action="{{ route('workplaces.setDefault', $workplace) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-primary">Als Standard setzen</button>
                            </form>
                        @endif
                    </td>
                    <td>{{ $workplace->trips_count ?? 0 }}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('workplaces.show', $workplace) }}" class="btn btn-outline-primary">Details</a>
                            <a href="{{ route('workplaces.edit', $workplace) }}" class="btn btn-outline-secondary">Bearbeiten</a>
                            <form action="{{ route('workplaces.destroy', $workplace) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Arbeitsplatz wirklich löschen?')">Löschen</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection