<!doctype html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ZipCodes') }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 20px;
        }
        
        .header {
            position: relative;
            text-align: center;
            padding: 20px 0;
            border-bottom: 2px solid #000;
            margin-bottom: 30px;
            min-height: 80px;
        }
        
        .header h2 {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            padding-top: 20px;
        }
        
        .header-logo {
            position: absolute;
            right: 0;
            top: 10px;
            max-width: 150px;
            max-height: 60px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        table th,
        table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        
        table th {
            font-weight: bold;
            background-color: #ddd;
        }
        
        .odd {
            background-color: lightgrey;
        }
        
        .even {
            background-color: grey;
        }
        
        footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #000;
            text-align: center;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo.png') }}" alt="Logo" class="header-logo" onerror="this.style.display='none'">
        <h2>@yield('page-title', config('app.name', 'ZipCodes'))</h2>
    </div>
    
    <main>
        @yield('content')
    </main>
    
    <footer>
        {{ config('app.name', 'ZipCodes') }} | Generálva: {{ now()->format('Y-m-d H:i') }}
    </footer>
</body>
</html>
