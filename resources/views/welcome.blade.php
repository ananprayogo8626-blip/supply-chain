<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SupplyGuard — Global Supply Chain Risk Intelligence Platform</title>
    <meta name="description" content="Enterprise-grade supply chain risk monitoring platform powered by 6 live global APIs. Monitor 250+ countries in real-time.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --indigo: #f47521;
            --indigo-light: #fb923c;
            --cyan: #dc2626;
            --emerald: #10b981;
            --dark: #0d1521;
            --dark-2: #1a2535;
            --text-primary: #f8fafc;
            --text-muted: rgba(255,255,255,0.55);
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--dark);
            color: var(--text-primary);
            overflow-x: hidden;
            background: linear-gradient(160deg, #0d1521 0%, #1a2535 40%, #0d1b2a 100%);
        }

        /* ===================== NAVBAR ===================== */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            padding: 16px 0;
            background: rgba(15,12,41,0.85);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255,255,255,0.06);
            transition: all 0.3s ease;
        }
        .nav-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .nav-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        .nav-logo-icon {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #f47521, #dc2626);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 20px rgba(244,117,33,0.4);
        }
        .nav-logo-text {
            font-size: 20px;
            font-weight: 800;
            color: white;
            letter-spacing: -0.5px;
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 32px;
            list-style: none;
        }
        .nav-links a {
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.2s;
        }
        .nav-links a:hover { color: white; }
        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .btn-ghost {
            padding: 8px 20px;
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 8px;
            color: white;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s;
            background: transparent;
        }
        .btn-ghost:hover { background: rgba(255,255,255,0.08); border-color: rgba(255,255,255,0.35); }
        .btn-solid {
            padding: 8px 20px;
            background: linear-gradient(135deg, #f47521, #dc2626);
            border-radius: 8px;
            color: white;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s;
            box-shadow: 0 4px 15px rgba(244,117,33,0.35);
        }
        .btn-solid:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(244,117,33,0.5); }

        /* ===================== HERO ===================== */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            padding: 100px 24px 60px;
        }

        /* Hero gradient */
        .hero-bg {
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 80% at 50% -20%, rgba(244,117,33,0.25) 0%, transparent 70%),
                radial-gradient(ellipse 60% 60% at 80% 80%, rgba(220,38,38,0.15) 0%, transparent 60%),
                linear-gradient(180deg, #0d1521 0%, #1a2535 50%, #0d1521 100%);
        }

        /* Grid pattern */
        .hero-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            mask-image: radial-gradient(ellipse 80% 80% at 50% 50%, black 40%, transparent 80%);
        }

        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 900px;
            text-align: center;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            background: rgba(244,117,33,0.15);
            border: 1px solid rgba(244,117,33,0.3);
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            color: #fb923c;
            margin-bottom: 28px;
            letter-spacing: 0.5px;
        }
        .hero-badge-dot {
            width: 6px;
            height: 6px;
            background: #fb923c;
            border-radius: 50%;
            animation: pulse 2s ease infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.5; transform: scale(0.8); }
        }

        .hero-title {
            font-size: clamp(40px, 7vw, 80px);
            font-weight: 900;
            line-height: 1.08;
            letter-spacing: -2px;
            margin-bottom: 24px;
        }
        .hero-title-grad {
            background: linear-gradient(135deg, #fb923c 0%, #f47521 50%, #dc2626 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-sub {
            font-size: 18px;
            color: var(--text-muted);
            line-height: 1.7;
            max-width: 600px;
            margin: 0 auto 40px;
        }

        .hero-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 60px;
        }
        .hero-btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 16px 32px;
            background: linear-gradient(135deg, #f47521, #dc2626);
            color: white;
            text-decoration: none;
            font-size: 15px;
            font-weight: 700;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(244,117,33,0.4);
            transition: all 0.25s ease;
        }
        .hero-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 12px 40px rgba(244,117,33,0.55); }
        .hero-btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 16px 32px;
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
            text-decoration: none;
            font-size: 15px;
            font-weight: 600;
            border-radius: 12px;
            backdrop-filter: blur(10px);
            background: rgba(255,255,255,0.06);
            transition: all 0.25s ease;
        }
        .hero-btn-secondary:hover { background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.35); }

        /* Stats strip */
        .hero-stats {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 40px;
            flex-wrap: wrap;
        }
        .hero-stat {
            text-align: center;
        }
        .hero-stat-num {
            font-size: 32px;
            font-weight: 800;
            background: linear-gradient(135deg, white, rgba(255,255,255,0.7));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero-stat-label {
            font-size: 12px;
            color: rgba(255,255,255,0.4);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .hero-stat-divider {
            width: 1px;
            height: 40px;
            background: rgba(255,255,255,0.1);
        }

        /* ===================== SECTION ===================== */
        section {
            padding: 100px 24px;
        }
        .section-inner {
            max-width: 1200px;
            margin: 0 auto;
        }
        .section-label {
            display: inline-block;
            font-size: 12px;
            font-weight: 700;
            color: #fb923c;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 16px;
        }
        .section-title {
            font-size: clamp(28px, 4vw, 48px);
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -1px;
            margin-bottom: 16px;
        }
        .section-sub {
            font-size: 16px;
            color: var(--text-muted);
            line-height: 1.7;
            max-width: 560px;
        }

        /* ===================== FEATURES ===================== */
        #features {
            background: linear-gradient(180deg, #0d1521 0%, #111d2e 100%);
        }
        .features-header {
            text-align: center;
            margin-bottom: 64px;
        }
        .features-header .section-sub { margin: 0 auto; }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
        }
        .feature-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 20px;
            padding: 32px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(244,117,33,0.5), transparent);
            opacity: 0;
            transition: opacity 0.3s;
        }
        .feature-card:hover { transform: translateY(-4px); border-color: rgba(244,117,33,0.3); background: rgba(255,255,255,0.06); }
        .feature-card:hover::before { opacity: 1; }

        .feature-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        .feature-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .feature-desc {
            font-size: 14px;
            color: var(--text-muted);
            line-height: 1.7;
        }

        /* ===================== APIs ===================== */
        #apis {
            background: #080e18;
        }
        .apis-header {
            text-align: center;
            margin-bottom: 56px;
        }
        .apis-header .section-sub { margin: 0 auto; }

        .apis-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
        }
        .api-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            padding: 24px;
            text-align: center;
            transition: all 0.25s ease;
        }
        .api-card:hover {
            background: rgba(255,255,255,0.07);
            border-color: rgba(244,117,33,0.3);
            transform: translateY(-3px);
        }
        .api-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 12px;
        }
        .api-name { font-size: 15px; font-weight: 700; margin-top: 12px; }
        .api-desc { font-size: 12px; color: var(--text-muted); margin-top: 4px; }

        /* ===================== DASHBOARD SCREENSHOT ===================== */
        #screenshot {
            background: linear-gradient(180deg, #090818 0%, #0f0c29 100%);
        }
        .screenshot-wrap {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 40px 80px rgba(0,0,0,0.6), 0 0 0 1px rgba(255,255,255,0.08);
            margin-top: 48px;
        }
        .screenshot-bar {
            background: rgba(30,27,75,0.95);
            padding: 14px 18px;
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .screenshot-dot { width: 12px; height: 12px; border-radius: 50%; }
        .screenshot-url {
            flex: 1;
            background: rgba(255,255,255,0.06);
            border-radius: 6px;
            padding: 5px 12px;
            font-size: 12px;
            color: rgba(255,255,255,0.4);
            margin: 0 12px;
        }
        .screenshot-content {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            padding: 40px;
            min-height: 320px;
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            align-items: flex-start;
        }

        /* Mock dashboard cards */
        .mock-card {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            padding: 20px;
            flex: 1;
            min-width: 140px;
        }
        .mock-card-label { font-size: 10px; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 1px; }
        .mock-card-value { font-size: 28px; font-weight: 800; margin-top: 8px; }
        .mock-card-trend { font-size: 11px; margin-top: 4px; }
        .mock-chart {
            flex: 2;
            min-width: 280px;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }
        .mock-chart-title { font-size: 13px; font-weight: 600; margin-bottom: 16px; }
        .mock-bars { display: flex; align-items: flex-end; gap: 6px; height: 80px; }
        .mock-bar {
            flex: 1;
            border-radius: 4px 4px 0 0;
            transition: height 0.3s;
        }

        /* ===================== STATISTICS ===================== */
        #statistics {
            background: linear-gradient(135deg, rgba(244,117,33,0.08) 0%, rgba(220,38,38,0.05) 100%);
            border-top: 1px solid rgba(255,255,255,0.06);
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 32px;
            text-align: center;
        }
        .stat-box-num {
            font-size: 56px;
            font-weight: 900;
            line-height: 1;
            background: linear-gradient(135deg, white, rgba(255,255,255,0.7));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .stat-box-label {
            font-size: 14px;
            color: var(--text-muted);
            margin-top: 8px;
        }

        /* ===================== CTA ===================== */
        .cta-section {
            text-align: center;
            padding: 100px 24px;
            background: #090818;
            position: relative;
            overflow: hidden;
        }
        .cta-section::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 600px;
            height: 400px;
            background: radial-gradient(ellipse, rgba(244,117,33,0.2) 0%, transparent 70%);
            pointer-events: none;
        }
        .cta-inner { position: relative; z-index: 1; }
        .cta-title {
            font-size: clamp(32px, 5vw, 56px);
            font-weight: 900;
            letter-spacing: -1.5px;
            margin-bottom: 20px;
        }
        .cta-sub {
            font-size: 17px;
            color: var(--text-muted);
            margin-bottom: 40px;
        }

        /* ===================== FOOTER ===================== */
        footer {
            background: #05040f;
            border-top: 1px solid rgba(255,255,255,0.06);
            padding: 48px 24px;
        }
        .footer-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 24px;
        }
        .footer-brand {
            font-size: 18px;
            font-weight: 800;
            color: white;
        }
        .footer-copy {
            font-size: 13px;
            color: rgba(255,255,255,0.3);
            margin-top: 4px;
        }
        .footer-links {
            display: flex;
            gap: 24px;
        }
        .footer-links a {
            color: rgba(255,255,255,0.4);
            text-decoration: none;
            font-size: 13px;
            transition: color 0.2s;
        }
        .footer-links a:hover { color: white; }

        /* Scroll reveal */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.7s ease, transform 0.7s ease;
        }
        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        @media (max-width: 768px) {
            .nav-links { display: none; }
            .hero-stat-divider { display: none; }
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav id="navbar">
        <div class="nav-inner">
            <a href="/" class="nav-logo">
                <div class="nav-logo-icon">
                    <svg width="22" height="22" fill="none" stroke="white" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="nav-logo-text">SupplyGuard</span>
            </a>

            <ul class="nav-links">
                <li><a href="#features">Features</a></li>
                <li><a href="#apis">Live APIs</a></li>
                <li><a href="#statistics">Statistics</a></li>
                <li><a href="#screenshot">Dashboard</a></li>
            </ul>

            <div class="nav-actions">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-solid">Go to Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn-solid">Sign In</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero">
        <div class="hero-bg"></div>
        <div class="hero-grid"></div>
        <div class="hero-content">
            <div class="hero-badge">
                <div class="hero-badge-dot"></div>
                Live Monitoring — 250+ Countries
            </div>

            <h1 class="hero-title">
                Global Supply Chain<br>
                <span class="hero-title-grad">Risk Intelligence</span>
            </h1>

            <p class="hero-sub">
                Enterprise-grade real-time risk monitoring powered by 6 live global APIs. Track weather disruptions, economic volatility, currency fluctuations, and port congestion worldwide.
            </p>

            <div class="hero-actions">
                @auth
                    <a href="{{ route('dashboard') }}" class="hero-btn-primary">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        Open Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hero-btn-primary">
                        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        Sign In
                    </a>
                @endauth
            </div>

            <div class="hero-stats">
                <div class="hero-stat">
                    <div class="hero-stat-num">250+</div>
                    <div class="hero-stat-label">Countries Monitored</div>
                </div>
                <div class="hero-stat-divider"></div>
                <div class="hero-stat">
                    <div class="hero-stat-num">6</div>
                    <div class="hero-stat-label">Live Data APIs</div>
                </div>
                <div class="hero-stat-divider"></div>
                <div class="hero-stat">
                    <div class="hero-stat-num">Real-Time</div>
                    <div class="hero-stat-label">Risk Scoring</div>
                </div>
                <div class="hero-stat-divider"></div>
                <div class="hero-stat">
                    <div class="hero-stat-num">4</div>
                    <div class="hero-stat-label">Risk Levels</div>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURES -->
    <section id="features">
        <div class="section-inner">
            <div class="features-header reveal">
                <span class="section-label">Platform Features</span>
                <h2 class="section-title">Everything You Need to<br>Manage Supply Chain Risk</h2>
                <p class="section-sub">Real-time intelligence across weather, economics, currency, news, and port operations — all in one dashboard.</p>
            </div>

            <div class="features-grid">
                <div class="feature-card reveal">
                    <div class="feature-icon" style="background: rgba(79,70,229,0.15);">
                        <svg width="24" height="24" fill="none" stroke="#818cf8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <h3 class="feature-title">Risk Score Engine</h3>
                    <p class="feature-desc">Automated risk calculation across Low, Medium, High, and Critical levels using weighted multi-factor analysis.</p>
                </div>

                <div class="feature-card reveal">
                    <div class="feature-icon" style="background: rgba(6,182,212,0.15);">
                        <svg width="24" height="24" fill="none" stroke="#06b6d4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
                    </div>
                    <h3 class="feature-title">Weather Monitoring</h3>
                    <p class="feature-desc">Real-time weather data for 250+ countries via Open-Meteo API. Storm risk, temperature extremes, and humidity tracking.</p>
                </div>

                <div class="feature-card reveal">
                    <div class="feature-icon" style="background: rgba(16,185,129,0.15);">
                        <svg width="24" height="24" fill="none" stroke="#10b981" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                    <h3 class="feature-title">Economic Intelligence</h3>
                    <p class="feature-desc">World Bank API integration for GDP, inflation, trade balance, and population data across all monitored economies.</p>
                </div>

                <div class="feature-card reveal">
                    <div class="feature-icon" style="background: rgba(245,158,11,0.15);">
                        <svg width="24" height="24" fill="none" stroke="#f59e0b" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="feature-title">Currency Tracking</h3>
                    <p class="feature-desc">Live exchange rates from ExchangeRate API with volatility scoring and trend analysis for supply chain cost assessment.</p>
                </div>

                <div class="feature-card reveal">
                    <div class="feature-icon" style="background: rgba(239,68,68,0.15);">
                        <svg width="24" height="24" fill="none" stroke="#ef4444" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 12H3l9-9 9 9h-2M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7M9 21V12h6v9"/></svg>
                    </div>
                    <h3 class="feature-title">Port Intelligence</h3>
                    <p class="feature-desc">Track 100+ major global ports, congestion levels, and operational status across key maritime trade routes.</p>
                </div>

                <div class="feature-card reveal">
                    <div class="feature-icon" style="background: rgba(139,92,246,0.15);">
                        <svg width="24" height="24" fill="none" stroke="#8b5cf6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                    </div>
                    <h3 class="feature-title">News Sentiment</h3>
                    <p class="feature-desc">GNews API integration for supply chain related news with automated sentiment scoring and country risk attribution.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- LIVE APIs -->
    <section id="apis">
        <div class="section-inner">
            <div class="apis-header reveal">
                <span class="section-label">Data Sources</span>
                <h2 class="section-title">Powered by 6 Live Global APIs</h2>
                <p class="section-sub">All data is fetched in real-time from authoritative global data sources — no dummy data, no outdated information.</p>
            </div>
            <div class="apis-grid">
                <div class="api-card reveal">
                    <div style="font-size:32px;">🌍</div>
                    <div class="api-name">REST Countries</div>
                    <div class="api-desc">250+ country profiles, capitals, flags</div>
                    <div class="api-badge" style="background:rgba(16,185,129,0.15);color:#34d399;">
                        <span style="width:6px;height:6px;background:#34d399;border-radius:50%;display:inline-block;"></span>
                        Live
                    </div>
                </div>
                <div class="api-card reveal">
                    <div style="font-size:32px;">🌤️</div>
                    <div class="api-name">Open-Meteo</div>
                    <div class="api-desc">Real-time weather & storm forecasts</div>
                    <div class="api-badge" style="background:rgba(6,182,212,0.15);color:#22d3ee;">
                        <span style="width:6px;height:6px;background:#22d3ee;border-radius:50%;display:inline-block;"></span>
                        Live
                    </div>
                </div>
                <div class="api-card reveal">
                    <div style="font-size:32px;">🏦</div>
                    <div class="api-name">World Bank</div>
                    <div class="api-desc">GDP, inflation, trade & population</div>
                    <div class="api-badge" style="background:rgba(16,185,129,0.15);color:#34d399;">
                        <span style="width:6px;height:6px;background:#34d399;border-radius:50%;display:inline-block;"></span>
                        Live
                    </div>
                </div>
                <div class="api-card reveal">
                    <div style="font-size:32px;">💱</div>
                    <div class="api-name">ExchangeRate</div>
                    <div class="api-desc">Live FX rates & currency volatility</div>
                    <div class="api-badge" style="background:rgba(245,158,11,0.15);color:#fbbf24;">
                        <span style="width:6px;height:6px;background:#fbbf24;border-radius:50%;display:inline-block;"></span>
                        Live
                    </div>
                </div>
                <div class="api-card reveal">
                    <div style="font-size:32px;">📰</div>
                    <div class="api-name">GNews</div>
                    <div class="api-desc">Supply chain news & sentiment</div>
                    <div class="api-badge" style="background:rgba(139,92,246,0.15);color:#a78bfa;">
                        <span style="width:6px;height:6px;background:#a78bfa;border-radius:50%;display:inline-block;"></span>
                        Live
                    </div>
                </div>
                <div class="api-card reveal">
                    <div style="font-size:32px;">🗺️</div>
                    <div class="api-name">OpenStreetMap</div>
                    <div class="api-desc">Global map & port geocoding</div>
                    <div class="api-badge" style="background:rgba(16,185,129,0.15);color:#34d399;">
                        <span style="width:6px;height:6px;background:#34d399;border-radius:50%;display:inline-block;"></span>
                        Live
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- STATISTICS -->
    <section id="statistics">
        <div class="section-inner">
            <div class="stats-grid reveal">
                <div>
                    <div class="stat-box-num">250+</div>
                    <div class="stat-box-label">Countries Monitored</div>
                </div>
                <div>
                    <div class="stat-box-num">100+</div>
                    <div class="stat-box-label">Global Ports Tracked</div>
                </div>
                <div>
                    <div class="stat-box-num">150+</div>
                    <div class="stat-box-label">News Articles/Day</div>
                </div>
                <div>
                    <div class="stat-box-num">6</div>
                    <div class="stat-box-label">Live Data APIs</div>
                </div>
                <div>
                    <div class="stat-box-num">4</div>
                    <div class="stat-box-label">Risk Categories</div>
                </div>
            </div>
        </div>
    </section>

    <!-- DASHBOARD SCREENSHOT (mock) -->
    <section id="screenshot">
        <div class="section-inner">
            <div class="reveal" style="text-align:center;margin-bottom:0;">
                <span class="section-label">Dashboard Preview</span>
                <h2 class="section-title">Enterprise Risk Dashboard</h2>
                <p class="section-sub" style="margin:0 auto;">Inspired by Power BI, SAP Analytics, and Grafana — built for supply chain professionals.</p>
            </div>

            <div class="screenshot-wrap reveal">
                <div class="screenshot-bar">
                    <div class="screenshot-dot" style="background:#ff5f57;"></div>
                    <div class="screenshot-dot" style="background:#ffbd2e;"></div>
                    <div class="screenshot-dot" style="background:#28ca41;"></div>
                    <div class="screenshot-url">app.supplyguard.io/dashboard</div>
                </div>
                <div class="screenshot-content">
                    <div class="mock-card">
                        <div class="mock-card-label">Countries</div>
                        <div class="mock-card-value" style="color:#818cf8;">250</div>
                        <div class="mock-card-trend" style="color:#34d399;">↑ Live</div>
                    </div>
                    <div class="mock-card">
                        <div class="mock-card-label">High Risk</div>
                        <div class="mock-card-value" style="color:#f87171;">42</div>
                        <div class="mock-card-trend" style="color:#f87171;">▲ Alert</div>
                    </div>
                    <div class="mock-card">
                        <div class="mock-card-label">Medium Risk</div>
                        <div class="mock-card-value" style="color:#fbbf24;">87</div>
                        <div class="mock-card-trend" style="color:#94a3b8;">Stable</div>
                    </div>
                    <div class="mock-card">
                        <div class="mock-card-label">Low Risk</div>
                        <div class="mock-card-value" style="color:#34d399;">121</div>
                        <div class="mock-card-trend" style="color:#34d399;">✓ Safe</div>
                    </div>
                    <div class="mock-chart">
                        <div class="mock-chart-title">Global Risk Distribution</div>
                        <div class="mock-bars">
                            <div class="mock-bar" style="height:40%;background:linear-gradient(to top,#34d399,#6ee7b7);"></div>
                            <div class="mock-bar" style="height:65%;background:linear-gradient(to top,#fbbf24,#fde68a);"></div>
                            <div class="mock-bar" style="height:80%;background:linear-gradient(to top,#f87171,#fca5a5);"></div>
                            <div class="mock-bar" style="height:30%;background:linear-gradient(to top,#ef4444,#dc2626);"></div>
                            <div class="mock-bar" style="height:55%;background:linear-gradient(to top,#fbbf24,#fde68a);"></div>
                            <div class="mock-bar" style="height:70%;background:linear-gradient(to top,#f87171,#fca5a5);"></div>
                            <div class="mock-bar" style="height:45%;background:linear-gradient(to top,#34d399,#6ee7b7);"></div>
                            <div class="mock-bar" style="height:90%;background:linear-gradient(to top,#ef4444,#dc2626);"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <div class="cta-section">
        <div class="cta-inner reveal">
            <h2 class="cta-title">Ready to Monitor Your<br>Supply Chain?</h2>
            <p class="cta-sub">Join SupplyGuard and get real-time risk intelligence for 250+ countries.</p>
            @auth
                <a href="{{ route('dashboard') }}" class="hero-btn-primary" style="display:inline-flex;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Open Dashboard
                </a>
            @else
                <a href="{{ route('register') }}" class="hero-btn-primary" style="display:inline-flex;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    Get Started Free
                </a>
            @endauth
        </div>
    </div>

    <!-- FOOTER -->
    <footer>
        <div class="footer-inner">
            <div>
                <div class="footer-brand">SupplyGuard</div>
                <div class="footer-copy">© {{ date('Y') }} Global Supply Chain Risk Intelligence Platform</div>
            </div>
            <div class="footer-links">
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}">Register</a>
                <a href="#features">Features</a>
                <a href="#apis">APIs</a>
            </div>
        </div>
    </footer>

    <script>
        // Scroll reveal
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('visible');
                }
            });
        }, { threshold: 0.1 });
        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const nav = document.getElementById('navbar');
            if (window.scrollY > 60) {
                nav.style.padding = '10px 0';
                nav.style.boxShadow = '0 4px 30px rgba(0,0,0,0.4)';
            } else {
                nav.style.padding = '16px 0';
                nav.style.boxShadow = 'none';
            }
        });
    </script>
</body>
</html>
