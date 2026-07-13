<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SupplyGuard') }} — Risk Intelligence Platform</title>

    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(160deg, #3A3A38 0%, #8D6654 30%, #D87942 55%, #FF6B00 75%, #6F130A 90%, #130B0B 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Animated background orbs */
        body::before {
            content: '';
            position: fixed;
            top: -20%;
            left: -10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(244,117,33,0.2) 0%, transparent 70%);
            border-radius: 50%;
            animation: floatOrb1 12s ease-in-out infinite;
            pointer-events: none;
        }
        body::after {
            content: '';
            position: fixed;
            bottom: -20%;
            right: -10%;
            width: 700px;
            height: 700px;
            background: radial-gradient(circle, rgba(220,38,38,0.15) 0%, transparent 70%);
            border-radius: 50%;
            animation: floatOrb2 15s ease-in-out infinite;
            pointer-events: none;
        }

        @keyframes floatOrb1 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33%       { transform: translate(60px, -40px) scale(1.1); }
            66%       { transform: translate(-30px, 50px) scale(0.95); }
        }
        @keyframes floatOrb2 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50%       { transform: translate(-50px, -60px) scale(1.08); }
        }

        /* Grid pattern overlay */
        .grid-overlay {
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            pointer-events: none;
        }

        /* Split layout */
        .auth-wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
            position: relative;
            z-index: 1;
        }

        /* Left branding panel */
        .auth-brand {
            display: none;
            flex: 1;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            padding: 60px;
            position: relative;
        }
        @media (min-width: 1024px) {
            .auth-brand { display: flex; }
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 48px;
        }
        .brand-logo-icon {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, #f47521, #dc2626);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 30px rgba(244,117,33,0.5);
        }
        .brand-logo-text {
            font-size: 26px;
            font-weight: 800;
            color: white;
            letter-spacing: -0.5px;
        }
        .brand-logo-text span {
            display: block;
            font-size: 11px;
            font-weight: 400;
            color: rgba(255,255,255,0.5);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .brand-headline {
            font-size: 42px;
            font-weight: 800;
            color: white;
            line-height: 1.15;
            margin-bottom: 20px;
            letter-spacing: -1px;
        }
        .brand-headline em {
            font-style: normal;
            background: linear-gradient(90deg, #fb923c, #f47521, #dc2626);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .brand-sub {
            font-size: 15px;
            color: rgba(255,255,255,0.55);
            line-height: 1.7;
            max-width: 400px;
            margin-bottom: 48px;
        }

        /* Stats row */
        .brand-stats {
            display: flex;
            gap: 32px;
        }
        .stat-item {
            text-align: left;
        }
        .stat-num {
            font-size: 28px;
            font-weight: 800;
            color: white;
        }
        .stat-label {
            font-size: 11px;
            color: rgba(255,255,255,0.4);
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        /* Feature pills */
        .feature-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 40px;
        }
        .feature-pill {
            padding: 6px 14px;
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 50px;
            font-size: 12px;
            color: rgba(255,255,255,0.6);
            display: flex;
            align-items: center;
            gap: 6px;
            backdrop-filter: blur(8px);
            background: rgba(255,255,255,0.04);
        }
        .feature-pill-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #4ade80;
            box-shadow: 0 0 8px #4ade80;
        }

        /* Right form panel */
        .auth-form-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            width: 100%;
        }
        @media (min-width: 1024px) {
            .auth-form-panel {
                width: 480px;
                min-width: 480px;
                background: rgba(255,255,255,0.04);
                backdrop-filter: blur(20px);
                border-left: 1px solid rgba(255,255,255,0.08);
                padding: 40px;
            }
        }

        .auth-card {
            width: 100%;
            max-width: 420px;
        }

        .auth-card-header {
            margin-bottom: 32px;
            text-align: center;
        }
        @media (min-width: 1024px) {
            .auth-card-header { text-align: left; }
        }

        .auth-card-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            justify-content: center;
            margin-bottom: 28px;
        }
        @media (min-width: 1024px) {
            .auth-card-logo { display: none; }
        }

        .auth-card-logo-icon {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #f47521, #dc2626);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-card-logo-text {
            font-size: 22px;
            font-weight: 800;
            color: white;
        }

        .auth-title {
            font-size: 26px;
            font-weight: 800;
            color: white;
            margin-bottom: 6px;
            letter-spacing: -0.5px;
        }
        .auth-subtitle {
            font-size: 14px;
            color: rgba(255,255,255,0.45);
        }

        /* Glassmorphism card container */
        .glass-card {
            background: rgba(24, 24, 30, 0.72);
            backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35);
        }

        /* Form elements */
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: rgba(255,255,255,0.7);
            margin-bottom: 8px;
            letter-spacing: 0.3px;
        }
        .form-input-wrap {
            position: relative;
        }
        .form-input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.3);
            pointer-events: none;
        }
        .form-input {
            width: 100%;
            background: rgba(24, 24, 30, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 12px 14px 12px 44px;
            color: white;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s ease;
            outline: none;
        }
        .form-input::placeholder { color: rgba(255,255,255,0.25); }
        .form-input:focus {
            border-color: rgba(244,117,33,0.6);
            background: rgba(24, 24, 30, 0.5);
            box-shadow: 0 0 0 3px rgba(244,117,33,0.15);
        }
        .form-input.has-right-icon {
            padding-right: 44px;
        }
        .form-input-right-btn {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: rgba(255,255,255,0.4);
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s;
        }
        .form-input-right-btn:hover { color: rgba(255,255,255,0.8); }

        .form-error {
            font-size: 12px;
            color: #f87171;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Checkbox */
        .form-check {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        .form-check input[type="checkbox"] {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            accent-color: #4f46e5;
            cursor: pointer;
        }
        .form-check-label {
            font-size: 13px;
            color: rgba(255,255,255,0.55);
            cursor: pointer;
        }

        /* Submit button */
        .btn-primary {
            width: 100%;
            background: linear-gradient(135deg, #f47521 0%, #dc2626 100%);
            color: white;
            font-weight: 700;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            border: none;
            border-radius: 12px;
            padding: 14px;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 4px 20px rgba(244,117,33,0.4);
            letter-spacing: 0.3px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
        }
        .btn-primary::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.15), transparent);
            opacity: 0;
            transition: opacity 0.2s;
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 8px 25px rgba(244,117,33,0.5); }
        .btn-primary:hover::before { opacity: 1; }
        .btn-primary:active { transform: translateY(0); }

        /* Links */
        .auth-link {
            color: #fb923c;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: color 0.2s;
        }
        .auth-link:hover { color: #fdba74; text-decoration: underline; }

        /* Divider */
        .form-divider {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        /* Session status */
        .session-status {
            background: rgba(16,185,129,0.15);
            border: 1px solid rgba(16,185,129,0.3);
            color: #34d399;
            font-size: 13px;
            padding: 10px 14px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        /* Bottom link */
        .auth-bottom {
            text-align: center;
            margin-top: 24px;
            font-size: 13px;
            color: rgba(255,255,255,0.4);
        }

        /* Animation */
        .glass-card {
            animation: fadeInUp 0.5s cubic-bezier(0.16,1,0.3,1) both;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Back to home */
        .back-home {
            position: fixed;
            top: 24px;
            left: 24px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: rgba(255,255,255,0.5);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: color 0.2s;
            z-index: 10;
        }
        .back-home:hover { color: white; }
    </style>
</head>
<body>
    <div class="grid-overlay"></div>

    <a href="/" class="back-home">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Home
    </a>

    <div class="auth-wrapper">
        <!-- Left Brand Panel -->
        <div class="auth-brand">
            <div class="brand-logo">
                <div class="brand-logo-icon">
                    <svg width="28" height="28" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="brand-logo-text">
                    SupplyGuard
                    <span>Risk Intelligence Platform</span>
                </div>
            </div>

            <h1 class="brand-headline">
                Monitor Global<br>
                <em>Supply Chain Risks</em><br>
                In Real Time
            </h1>
            <p class="brand-sub">
                Enterprise-grade supply chain risk intelligence powered by live data from World Bank, Open-Meteo, ExchangeRate, and GNews APIs.
            </p>

            <div class="brand-stats">
                <div class="stat-item">
                    <div class="stat-num">250+</div>
                    <div class="stat-label">Countries</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num">6</div>
                    <div class="stat-label">Live APIs</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num">Real-time</div>
                    <div class="stat-label">Risk Scoring</div>
                </div>
            </div>

            <div class="feature-pills">
                <div class="feature-pill"><div class="feature-pill-dot"></div> Weather Monitoring</div>
                <div class="feature-pill"><div class="feature-pill-dot"></div> Economic Data</div>
                <div class="feature-pill"><div class="feature-pill-dot"></div> Currency Tracking</div>
                <div class="feature-pill"><div class="feature-pill-dot"></div> Port Intelligence</div>
                <div class="feature-pill"><div class="feature-pill-dot"></div> News Sentiment</div>
            </div>
        </div>

        <!-- Right Form Panel -->
        <div class="auth-form-panel">
            <div class="auth-card">
                <!-- Mobile Logo -->
                <div class="auth-card-logo">
                    <div class="auth-card-logo-icon">
                        <svg width="24" height="24" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="auth-card-logo-text">SupplyGuard</div>
                </div>

                <div class="glass-card">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
