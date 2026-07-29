<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Report' }} - {{ config('brand.name') }}</title>
    <style>
        @page {
            margin: 20mm 15mm 25mm 15mm;
            font-family: DejaVu Sans, sans-serif;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11pt;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            border-bottom: 3px solid #1f6f43;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header .school-name {
            font-size: 20pt;
            font-weight: 700;
            color: #1f6f43;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .header .tagline {
            font-size: 9pt;
            color: #666;
            margin-top: 2px;
        }

        .header .company-name {
            font-size: 8pt;
            color: #999;
            font-style: italic;
            margin-top: 4px;
        }

        .report-title {
            text-align: center;
            font-size: 14pt;
            font-weight: 700;
            color: #1f6f43;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .footer {
            position: fixed;
            bottom: -20mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 6px;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="school-name">{{ config('brand.name') }}</div>
        <div class="tagline">{{ config('brand.tagline') }}</div>
        <div class="company-name">{{ config('brand.company_name') }}</div>
    </div>

    <div class="report-title">
        @yield('report-title')
    </div>

    <div class="content">
        @yield('content')
    </div>

    <div class="footer">
        Generated on {{ now()->format('F j, Y') }} &mdash; {{ config('brand.name') }} &bull; {{ config('brand.company_name') }}
    </div>

</body>
</html>
