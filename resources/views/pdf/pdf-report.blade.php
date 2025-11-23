<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realm Decree</title>

    <style>
        @page {
            margin: 0;
        }

        body {
            font-family: 'Georgia', 'Times New Roman', serif;
            background: #f4e8d8;
            color: #2d1810;
            margin: 0;
            padding: 0;
        }

        .page {
            padding: 60px;
            page-break-after: always;
        }

        .no-break {
            page-break-after: avoid;
        }

        .parchment {
            background: linear-gradient(135deg, #f9f3e8 0%, #ede3d0 100%);
            border: 15px solid #8b6f47;
            padding: 50px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        .parchment::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image:
                repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(139, 111, 71, 0.03) 2px, rgba(139, 111, 71, 0.03) 4px);
            pointer-events: none;
        }

        .seal {
            text-align: center;
            margin-bottom: 40px;
        }

        .seal-icon {
            font-size: 72px;
            color: #92400e;
            margin-bottom: 10px;
        }

        h1 {
            text-align: center;
            color: #92400e;
            font-size: 42px;
            margin: 0 0 10px;
            text-transform: uppercase;
            letter-spacing: 4px;
            font-weight: 700;
        }

        .subtitle {
            text-align: center;
            color: #6b5644;
            font-size: 16px;
            font-style: italic;
            margin-bottom: 40px;
            letter-spacing: 2px;
        }

        .decree-header {
            border-top: 3px double #92400e;
            border-bottom: 3px double #92400e;
            padding: 20px 0;
            margin: 40px 0;
            text-align: center;
        }

        .decree-header h2 {
            font-size: 36px;
            color: #92400e;
            margin: 0;
            font-weight: 700;
        }

        .score-badge {
            display: inline-block;
            background: #92400e;
            color: #f9f3e8;
            padding: 10px 30px;
            border-radius: 50px;
            font-size: 32px;
            font-weight: 700;
            margin: 20px 0;
        }

        .archetype-box {
            background: rgba(146, 64, 14, 0.08);
            border-left: 5px solid #92400e;
            padding: 20px;
            margin: 30px 0;
        }

        .archetype-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: #92400e;
            font-weight: 700;
        }

        .archetype-value {
            font-size: 24px;
            font-weight: 700;
        }

        .justification {
            margin: 40px 0;
            font-size: 16px;
            line-height: 1.9;
            text-align: justify;
        }

        .data-section h3 {
            margin-top: 40px;
            color: #92400e;
            font-size: 20px;
            border-bottom: 2px solid #92400e;
            padding-bottom: 10px;
        }

        .data-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .data-row {
            border-bottom: 1px solid #d4c4b0;
            padding: 12px 0;
            display: flex;
            justify-content: space-between;
        }

        .signature {
            margin-top: 40px;
            text-align: right;
            font-size: 18px;
            font-style: italic;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 3px double #92400e;
            text-align: center;
            font-size: 12px;
            color: #6b5644;
        }
    </style>
</head>

<body>

    <!-- PAGE 1 -->
    <div class="page">
        <div class="parchment">

            <div class="seal">
                <div class="seal-icon">🏰</div>
            </div>

            <h1>Royal Decree</h1>
            <div class="subtitle">By Order of the Maesters of the Citadel</div>

            <div class="decree-header">
                <h2>{{ $data['neighborhood'] }}</h2>
                <div class="score-badge">Score: {{ $data['score'] }}</div>
            </div>

            <div class="archetype-box">
                <div class="archetype-label">Thy Archetype</div>
                <div class="archetype-value">{{ $data['archetype'] }}</div>
            </div>

        </div>
    </div>

    <!-- PAGE 2 -->
    <div class="page no-break">
        <div class="parchment">

            <div class="justification">
                {{ $data['justification'] }}
            </div>

            @if (isset($data['kpis']))
                <div class="data-section">
                    <h3>Realm Statistics</h3>

                    @foreach ($data['kpis'] as $key => $value)
                        <div class="data-row">
                            <div>{{ ucwords(str_replace('_', ' ', $key)) }}</div>
                            <div><strong>{{ $value }}</strong></div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="signature">
                — The Maesters of Los Angeles
            </div>

            <div class="footer">
                <p>This decree was issued on {{ now()->format('F j, Y') }}</p>
                <p><strong>Winter is coming. Choose your stronghold wisely.</strong></p>
            </div>

        </div>
    </div>

</body>

</html>
