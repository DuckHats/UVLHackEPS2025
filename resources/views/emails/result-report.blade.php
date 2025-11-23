<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Realm Awaits</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Lato:wght@300;400;700&display=swap');

        body {
            font-family: 'Lato', Arial, sans-serif;
            background-color: #1a1a1a;
            color: #e5e5e5;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
            border: 2px solid #92400e;
            border-radius: 8px;
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #92400e 0%, #78350f 100%);
            padding: 40px 20px;
            text-align: center;
            border-bottom: 3px solid #fbbf24;
        }

        .header h1 {
            font-family: 'Cinzel', serif;
            color: #fbbf24;
            margin: 0;
            font-size: 32px;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .header p {
            color: #d1d5db;
            margin: 10px 0 0;
            font-size: 14px;
        }

        .content {
            padding: 40px 30px;
        }

        .decree-box {
            background: rgba(251, 191, 36, 0.05);
            border-left: 4px solid #fbbf24;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .decree-box h2 {
            font-family: 'Cinzel', serif;
            color: #fbbf24;
            margin: 0 0 15px;
            font-size: 24px;
        }

        .decree-box .score {
            font-size: 48px;
            font-weight: 700;
            color: #fbbf24;
            font-family: 'Cinzel', serif;
            margin: 10px 0;
        }

        .archetype {
            background: rgba(31, 41, 55, 0.8);
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            border: 1px solid #374151;
        }

        .archetype-label {
            font-family: 'Cinzel', serif;
            color: #fbbf24;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 5px;
        }

        .archetype-value {
            color: #ffffff;
            font-size: 18px;
            font-weight: 700;
        }

        .justification {
            background: rgba(17, 24, 39, 0.6);
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            font-size: 15px;
            line-height: 1.8;
            color: #d1d5db;
            border: 1px solid #374151;
        }

        .justification::first-letter {
            font-size: 48px;
            font-family: 'Cinzel', serif;
            color: #fbbf24;
            float: left;
            line-height: 1;
            margin-right: 8px;
        }

        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #92400e 0%, #78350f 100%);
            color: #ffffff;
            text-decoration: none;
            padding: 15px 40px;
            border-radius: 6px;
            font-family: 'Cinzel', serif;
            font-weight: 700;
            text-align: center;
            margin: 20px 0;
            border: 2px solid #fbbf24;
            transition: all 0.3s;
        }

        .footer {
            background: #111827;
            padding: 30px;
            text-align: center;
            border-top: 2px solid #374151;
            color: #9ca3af;
            font-size: 13px;
        }

        .divider {
            height: 2px;
            background: linear-gradient(90deg, transparent, #fbbf24, transparent);
            margin: 30px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🏰 Your Realm Awaits</h1>
            <p>The Maesters have spoken</p>
        </div>

        <div class="content">
            <div class="decree-box">
                <h2>{{ $data['neighborhood'] ?? 'Unknown Realm' }}</h2>
                <div class="score">{{ $data['score'] ?? '0' }}%</div>
                <p style="color: #9ca3af; margin: 0;">Compatibility Match</p>
            </div>

            <div class="archetype">
                <div class="archetype-label">Your Archetype</div>
                <div class="archetype-value">{{ $data['archetype'] ?? 'Unknown' }}</div>
            </div>

            <div class="divider"></div>

            <div class="justification">
                {{ $data['justification'] ?? 'The Maesters are still deliberating on your decree.' }}
            </div>

            <div class="divider"></div>

            <center>
                <a href="{{ url('/') }}" class="cta-button">
                    Consult the Maesters Again
                </a>
            </center>
        </div>

        <div class="footer">
            <p style="margin: 0 0 10px;">
                <strong>Winter is coming.</strong> Find your stronghold wisely.
            </p>
            <p style="margin: 0; font-size: 12px;">
                This raven was sent from the Citadel of Los Angeles Neighborhood Finder
            </p>
        </div>
    </div>
</body>

</html>
