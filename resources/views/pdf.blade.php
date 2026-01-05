<!doctype html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ZipCodes') }}</title>
    <style>
        @page {
            margin: 120px 40px 80px 40px;
        }
        
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }
        
        /* Fixed header on every page */
        .page-header {
            position: fixed;
            top: -100px;
            left: 0;
            right: 0;
            height: 60px;
            padding: 15px 40px;
            border-bottom: 2px solid #000;
            background-color: #fff;
        }
        
        .header-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 0;
            padding-top: 5px;
        }
        
        .header-logo {
            position: absolute;
            right: 40px;
            top: 10px;
            max-width: 120px;
            max-height: 40px;
        }
        
        /* Fixed footer on every page */
        .page-footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            height: 40px;
            padding: 10px 40px;
            border-top: 1px solid #000;
            background-color: #fff;
            text-align: center;
            font-size: 10px;
        }
        
        /* Remove all margins from main */
        main {
            margin: 0;
            padding: 0;
        }
        
        /* Table styling - tananyag szerint */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
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
        
        /* Page break control */
        tr {
            page-break-inside: avoid;
        }
        
        thead {
            display: table-header-group;
        }
    </style>
</head>
<body>
    <!-- Fixed Header - appears on every page -->
    <div class="page-header">
        @php
            $logoPath = public_path('images/logo.png');
            $logoExists = file_exists($logoPath);
        @endphp
        @if($logoExists)
            <img src="{{ $logoPath }}" alt="Logo" class="header-logo">
        @endif
        <div class="header-title">
            @yield('page-title', config('app.name', 'ZipCodes'))
        </div>
    </div>
    
    <!-- Fixed Footer - appears on every page -->
    <div class="page-footer">
        {{ config('app.name', 'ZipCodes') }} | Generálva: {{ now()->format('Y-m-d H:i') }}
    </div>
    
    <!-- Main content -->
    <main>
        @yield('content')
    </main>
</body>
</html>
