@extends('layout')

@section('title', 'E-Mail bestätigen - Fahrtkosten-Rechner')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">E-Mail-Adresse bestätigen</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong>Fast geschafft!</strong> 
                    Wir haben Ihnen eine Bestätigungs-E-Mail an <strong>{{ Auth::user()->email }}</strong> gesendet.
                </div>
                
                <p class="mb-4">
                    Bitte überprüfen Sie Ihr E-Mail-Postfach und klicken Sie auf den Bestätigungslink, 
                    um Ihr Konto zu aktivieren.
                </p>

                <div class="mb-3">
                    <p class="text-muted mb-2">Keine E-Mail erhalten?</p>
                    <form method="POST" action="{{ route('verification.send') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary">
                            Bestätigungs-E-Mail erneut senden
                        </button>
                    </form>
                </div>

                <hr>
                
                <div class="text-center">
                    <p class="mb-2 text-muted">Falsche E-Mail-Adresse?</p>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-link text-decoration-none">
                            Abmelden und erneut registrieren
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection