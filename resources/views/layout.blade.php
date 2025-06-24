<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Fahrtkosten-Rechner')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Swiss date format styling */
        .date-input-wrapper {
            position: relative;
        }
        
        .date-display {
            position: absolute;
            top: 0;
            left: 0;
            right: 40px;
            bottom: 0;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 0.375rem 0.75rem;
            pointer-events: none;
            z-index: 1;
            line-height: 1.5;
            color: #495057;
        }
        
        .date-input-wrapper input[type="date"] {
            position: relative;
            z-index: 2;
            background: transparent;
            color: transparent;
        }
        
        .date-input-wrapper input[type="date"]::-webkit-calendar-picker-indicator {
            opacity: 0;
            width: 20px;
            height: 20px;
            cursor: pointer;
        }
        
        .calendar-icon {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 3;
            pointer-events: auto;
            color: #6c757d;
            font-size: 16px;
            cursor: pointer;
        }
        
        .date-input-wrapper input[type="date"]:focus + .date-display {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        
        .date-input-wrapper.is-invalid .date-display {
            border-color: #dc3545;
        }
        
        .date-input-wrapper.is-invalid input[type="date"]:focus + .date-display {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">Fahrtkosten-Rechner</a>
            <div class="navbar-nav">
                <a class="nav-link" href="{{ route('trips.index') }}">Alle Fahrten</a>
                <a class="nav-link" href="{{ route('trips.create') }}">Neue Fahrt</a>
                <a class="nav-link" href="{{ route('workplaces.index') }}">Arbeitsplätze</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>