<!doctype html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <title>{{ config('app.name', 'ZipCodes') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 2px solid #000;
            margin-bottom: 30px;
        }
        .header h2 {
            font-size: 24px;
            font-weight: bold;
            margin-top: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table thead {
            background-color: #f0f0f0;
        }
        table th,
        table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            font-weight: bold;
            background-color: #e0e0e0;
        }
        .odd {
            background-color: #fafafa;
        }
        .even {
            background-color: #f5f5f5;
        }
        footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 9px;
            text-align: center;
            color: #666;
        }
        main {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ config('app.name', 'ZipCodes') }}</h2>
    </div>
    <main>
        @yield('content')
    </main>
    <footer>
        {{ config('app.name', 'ZipCodes') }} | Generálva: {{ now()->format('Y-m-d H:i') }}
    </footer>
</body>
</html>
