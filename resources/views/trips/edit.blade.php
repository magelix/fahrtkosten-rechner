@extends('layout')

@section('title', 'Fahrt bearbeiten')

@section('content')
<h1>Fahrt bearbeiten</h1>

<form action="{{ route('trips.update', $trip) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="row">
        <div class="col-md-12">
            <div class="mb-3">
                <label for="workplace_id" class="form-label">Arbeitsplatz</label>
                <select class="form-select @error('workplace_id') is-invalid @enderror" 
                        id="workplace_id" name="workplace_id" required>
                    <option value="">-- Arbeitsplatz auswählen --</option>
                    @foreach($workplaces as $workplace)
                        <option value="{{ $workplace->id }}" 
                                data-distance="{{ $workplace->default_distance_km }}"
                                data-cost="{{ $workplace->default_cost_per_km }}"
                                {{ old('workplace_id', $trip->workplace_id) == $workplace->id ? 'selected' : '' }}>
                            {{ $workplace->name }} - {{ $workplace->address }}
                        </option>
                    @endforeach
                </select>
                @error('workplace_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="mb-3">
                <label for="distance_km" class="form-label">Entfernung (km, einfach)</label>
                <input type="number" step="0.1" class="form-control @error('distance_km') is-invalid @enderror" 
                       id="distance_km" name="distance_km" value="{{ old('distance_km', $trip->distance_km) }}" required>
                @error('distance_km')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="mb-3">
                <label for="departure_date" class="form-label">Abreise-Datum</label>
                <input type="date" class="form-control @error('departure_date') is-invalid @enderror" 
                       id="departure_date" name="departure_date" value="{{ old('departure_date', $trip->departure_date->format('Y-m-d')) }}" required>
                @error('departure_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="mb-3">
                <label for="return_date" class="form-label">Rückkehr-Datum</label>
                <input type="date" class="form-control @error('return_date') is-invalid @enderror" 
                       id="return_date" name="return_date" value="{{ old('return_date', $trip->return_date->format('Y-m-d')) }}" required>
                @error('return_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="overnight_days" class="form-label">Anzahl Übernachtungen</label>
                <input type="number" min="0" class="form-control @error('overnight_days') is-invalid @enderror" 
                       id="overnight_days" name="overnight_days" value="{{ old('overnight_days', $trip->overnight_days) }}" required>
                @error('overnight_days')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Wird automatisch basierend auf den Daten errechnet, kann überschrieben werden</div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="mb-3">
                <label for="cost_per_km" class="form-label">Kosten pro Kilometer (CHF)</label>
                <input type="number" step="0.01" class="form-control @error('cost_per_km') is-invalid @enderror" 
                       id="cost_per_km" name="cost_per_km" value="{{ old('cost_per_km', $trip->cost_per_km) }}" required>
                @error('cost_per_km')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="mb-3">
        <button type="submit" class="btn btn-primary">Änderungen speichern</button>
        <a href="{{ route('trips.index') }}" class="btn btn-secondary">Abbrechen</a>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const workplaceSelect = document.getElementById('workplace_id');
    const distanceInput = document.getElementById('distance_km');
    const costInput = document.getElementById('cost_per_km');
    const departureInput = document.getElementById('departure_date');
    const returnInput = document.getElementById('return_date');
    const overnightInput = document.getElementById('overnight_days');
    
    function calculateOvernightDays() {
        const departureDate = new Date(departureInput.value);
        const returnDate = new Date(returnInput.value);
        
        if (departureDate && returnDate && returnDate > departureDate) {
            const timeDiff = returnDate - departureDate;
            const daysDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
            const overnights = Math.max(0, daysDiff);
            overnightInput.value = overnights;
        }
    }
    
    workplaceSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (selectedOption.value) {
            const distance = selectedOption.getAttribute('data-distance');
            const cost = selectedOption.getAttribute('data-cost');
            
            if (distance) distanceInput.value = distance;
            if (cost) costInput.value = cost;
        }
    });
    
    departureInput.addEventListener('change', calculateOvernightDays);
    returnInput.addEventListener('change', calculateOvernightDays);
});
</script>
@endsection