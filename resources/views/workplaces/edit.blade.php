@extends('layout')

@section('title', 'Arbeitsplatz bearbeiten')

@section('content')
<h1>Arbeitsplatz bearbeiten</h1>

<form action="{{ route('workplaces.update', $workplace) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="name" class="form-label">Name des Arbeitsplatzes</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                       id="name" name="name" value="{{ old('name', $workplace->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="mb-3">
                <label for="address" class="form-label">Adresse</label>
                <input type="text" class="form-control @error('address') is-invalid @enderror" 
                       id="address" name="address" value="{{ old('address', $workplace->address) }}" required>
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="default_distance_km" class="form-label">Standard-Entfernung (km, einfach)</label>
                <input type="number" step="0.1" class="form-control @error('default_distance_km') is-invalid @enderror" 
                       id="default_distance_km" name="default_distance_km" value="{{ old('default_distance_km', $workplace->default_distance_km) }}" required>
                @error('default_distance_km')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Diese Entfernung wird standardmäßig bei neuen Fahrten vorausgefüllt</div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="mb-3">
                <label for="default_cost_per_km" class="form-label">Standard-Kosten pro Kilometer (CHF)</label>
                <input type="number" step="0.01" class="form-control @error('default_cost_per_km') is-invalid @enderror" 
                       id="default_cost_per_km" name="default_cost_per_km" value="{{ old('default_cost_per_km', $workplace->default_cost_per_km) }}" required>
                @error('default_cost_per_km')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="mb-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ old('is_active', $workplace->is_active) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">
                Arbeitsplatz ist aktiv
            </label>
        </div>
        <div class="form-text">Inaktive Arbeitsplätze werden bei neuen Fahrten nicht zur Auswahl angezeigt</div>
    </div>

    <div class="mb-3">
        <button type="submit" class="btn btn-primary">Änderungen speichern</button>
        <a href="{{ route('workplaces.index') }}" class="btn btn-secondary">Abbrechen</a>
    </div>
</form>
@endsection