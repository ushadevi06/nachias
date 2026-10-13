<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NIKO Garments ERP &bull; Login</title>
    <link rel="icon" type="image/jpeg" href="{{ url('assets/images/fav.jpeg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Public+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('assets/css/core.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/demo.css') }}">
    <link rel="stylesheet" href="{{ url('assets/fonts/iconify-icons.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/node-waves.css') }}">

    <style>
        :root {
            --niko-purple-primary: #6352e8;
            --niko-purple-hover: #503ee0;
            --niko-purple-dark: #372385;
            --niko-purple-soft: #f3f1fd;
            --niko-purple-border: #e7e3f8;
            --niko-text-dark: #191235;
            --niko-text-muted: #6e6888;
            --niko-input-bg: #ffffff;
            --niko-input-border: #e2def2;
        }

        * {
            box-sizing: border-box;
        }

        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family: 'Plus Jakarta Sans', 'Public Sans', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #0d0822;
            overflow-x: hidden;
        }

        /* Master Full-Screen Split Layout */
        .login-master-container {
            position: relative;
            width: 100%;
            min-height: 100vh;
            display: flex;
            background-color: #0d0822;
            overflow: hidden;
        }

        /* LEFT COLUMN (60% Desktop): Hero Factory Visual + Storytelling */
        .login-left-pane {
            position: relative;
            width: 60%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            background-color: #0d0822;
        }

        /* Background Factory Image Layer */
        .left-bg-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center 30%;
            display: block;
            filter: contrast(1.04) saturate(1.08);
            z-index: 1;
        }

        /* Left Gradient Overlays for Readability & Depth */
        .left-bg-gradient {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(180deg, 
                rgba(13, 8, 34, 0.65) 0%, 
                rgba(13, 8, 34, 0.15) 30%, 
                rgba(13, 8, 34, 0.25) 55%, 
                rgba(13, 8, 34, 0.85) 80%, 
                rgba(10, 6, 26, 0.98) 100%),
                linear-gradient(90deg, 
                rgba(13, 8, 34, 0.6) 0%, 
                rgba(13, 8, 34, 0.2) 40%, 
                rgba(13, 8, 34, 0.1) 70%, 
                rgba(13, 8, 34, 0.5) 100%);
            z-index: 2;
            pointer-events: none;
        }

        /* Foreground Content on Left */
        .left-content-wrap {
            position: relative;
            z-index: 4;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
            min-height: 100vh;
            padding: 2.75rem 3.5rem 2rem 3.5rem;
        }

        /* Top-Left Brand Logo Block */
        .hero-brand-header {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .hexagon-logo-badge {
            width: 50px;
            height: 50px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.08);
            border: 2px solid rgba(255, 255, 255, 0.85);
            border-radius: 14px;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
            padding: 8px;
        }

        .hero-brand-name {
            font-size: 1.85rem;
            font-weight: 800;
            letter-spacing: 4px;
            color: #ffffff;
            line-height: 1;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.4);
        }

        .hero-brand-tagline {
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 2px;
            color: rgba(230, 222, 255, 0.85);
            text-transform: uppercase;
            margin-top: 4px;
        }

        /* Left Mid Hero Headline Area */
        .hero-headline-area {
            max-width: 540px;
            margin-top: auto;
            margin-bottom: 2.5rem;
        }

        .fabric-pill-tag {
            display: inline-block;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #a999ff;
            margin-bottom: 0.85rem;
        }

        .hero-main-title {
            font-size: 2.85rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.15;
            letter-spacing: -0.025em;
            margin: 0 0 0.85rem 0;
            text-shadow: 0 4px 18px rgba(0, 0, 0, 0.45);
        }

        .hero-main-title .gradient-word {
            background: linear-gradient(135deg, #a796ff 0%, #7663f7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-main-desc {
            font-size: 1rem;
            line-height: 1.55;
            color: rgba(235, 230, 255, 0.88);
            margin: 0;
            max-width: 440px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.35);
        }

        /* Handwritten Cursive Script Slogan */
        .script-quote-floating {
            position: absolute;
            top: 15%;
            right: 8%;
            font-family: 'Caveat', cursive;
            font-size: 1.85rem;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.1;
            text-align: right;
            transform: rotate(-8deg);
            text-shadow: 0 4px 14px rgba(0, 0, 0, 0.5);
            pointer-events: none;
            z-index: 3;
            opacity: 0.92;
        }

        /* Bottom Curved Workflow Glass Panel */
        .bottom-workflow-glass {
            position: relative;
            background: linear-gradient(180deg, rgba(20, 12, 45, 0.65) 0%, rgba(14, 8, 34, 0.92) 100%);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 20px;
            padding: 1.4rem 1.6rem 1rem 1.6rem;
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.35);
        }

        .workflow-cards-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
        }

        .wf-card-item {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .wf-card-header {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .wf-icon-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #c4b6ff;
            flex-shrink: 0;
        }

        .wf-card-title {
            font-size: 0.74rem;
            font-weight: 800;
            letter-spacing: 1.4px;
            color: #ffffff;
            text-transform: uppercase;
        }

        .wf-card-sub {
            font-size: 0.7rem;
            line-height: 1.35;
            color: rgba(215, 206, 245, 0.75);
            margin: 0;
        }

        .bottom-company-subline {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 1rem;
            padding-top: 0.75rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            font-size: 0.68rem;
            letter-spacing: 2px;
            color: rgba(215, 206, 245, 0.6);
            text-transform: uppercase;
            font-weight: 700;
        }

        .bottom-company-subline::before {
            content: '';
            display: inline-block;
            width: 24px;
            height: 1.5px;
            background: #7d6cf0;
        }

        /* RIGHT COLUMN (40% Desktop): Luxury Satin Silk Fabric + Floating Card */
        .login-right-pane {
            position: relative;
            width: 40%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 3.5rem;
            background: linear-gradient(135deg, #f8f6fd 0%, #ece8f9 50%, #dfd8f5 100%);
            overflow: hidden;
            z-index: 5;
        }

        /* Satin Silk Wave Layers in Background of Right Pane */
        .satin-wave-top {
            position: absolute;
            top: -15%;
            right: -20%;
            width: 140%;
            height: 80%;
            background: radial-gradient(circle at 70% 30%, rgba(255, 255, 255, 0.95) 0%, rgba(235, 228, 252, 0.7) 45%, rgba(195, 180, 245, 0.35) 85%);
            transform: rotate(-18deg);
            pointer-events: none;
            z-index: 1;
        }

        .satin-wave-bottom {
            position: absolute;
            bottom: -20%;
            right: -10%;
            width: 130%;
            height: 70%;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(220, 208, 250, 0.7) 50%, rgba(180, 160, 235, 0.5) 100%);
            border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
            pointer-events: none;
            z-index: 1;
        }

        /* Top-Right Secure Workspace Badge */
        .secure-badge-corner {
            position: absolute;
            top: 2rem;
            right: 2.5rem;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #4c4270;
            background: rgba(255, 255, 255, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(8px);
            padding: 6px 14px;
            border-radius: 999px;
            box-shadow: 0 4px 12px rgba(99, 82, 232, 0.08);
            z-index: 6;
        }

        .secure-green-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 8px #10b981;
            display: inline-block;
        }

        /* Decorative Shirt Button at Bottom Right Corner */
        .satin-button-decor {
            position: absolute;
            bottom: 2rem;
            right: 2rem;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: radial-gradient(circle at 35% 35%, #ffffff 0%, #e2daf5 70%, #bcb0df 100%);
            border: 1.5px solid #ffffff;
            box-shadow: 0 6px 14px rgba(80, 60, 150, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            pointer-events: none;
            z-index: 2;
        }

        .satin-button-decor::before, .satin-button-decor::after {
            content: '';
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: #8b79b5;
        }

        /* FLOATING WHITE CARD MATCHING MOCKUP */
        .mockup-floating-card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border-radius: 26px;
            padding: 2.75rem 2.5rem;
            box-shadow: 0 25px 60px rgba(45, 25, 95, 0.12), 0 4px 16px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.95);
        }

        /* Card Logo Header (Centered Hexagon Emblem) */
        .card-brand-center {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-bottom: 1.6rem;
        }

        .card-hexagon-emblem {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: #ffffff;
            border: 2px solid #5846e0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px;
            box-shadow: 0 8px 20px rgba(99, 82, 232, 0.16);
            margin-bottom: 0.75rem;
        }

        .card-niko-title {
            font-size: 1.85rem;
            font-weight: 800;
            letter-spacing: 4px;
            color: #2b1c78;
            line-height: 1;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        }

        .card-niko-subtitle {
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 2px;
            color: #7d759e;
            text-transform: uppercase;
            margin-top: 5px;
        }

        /* Welcome Text */
        .card-heading-title {
            font-size: 1.65rem;
            font-weight: 800;
            color: #1a1235;
            margin: 0 0 0.35rem 0;
            letter-spacing: -0.02em;
        }

        .card-heading-title .purple-back {
            color: #6352e8;
        }

        .card-heading-desc {
            font-size: 0.88rem;
            color: #736d8d;
            margin: 0 0 1.6rem 0;
        }

        /* Form Inputs */
        .field-wrap-box {
            position: relative;
            margin-bottom: 1.15rem;
        }

        .field-icon-left {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9c95b2;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            z-index: 2;
        }

        .field-input-control {
            width: 100%;
            height: 50px;
            padding: 0 46px 0 48px;
            background-color: #ffffff;
            border: 1.5px solid var(--niko-input-border);
            border-radius: 12px;
            font-size: 0.94rem;
            color: #1b1236;
            transition: all 0.2s ease-in-out;
            outline: none;
        }

        .field-input-control::placeholder {
            color: #a7a0bd;
        }

        .field-input-control:focus {
            border-color: var(--niko-purple-primary);
            box-shadow: 0 0 0 4px rgba(99, 82, 232, 0.14);
        }

        .field-icon-eye {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #9c95b2;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
            transition: color 0.15s;
        }

        .field-icon-eye:hover {
            color: var(--niko-purple-primary);
        }

        /* Remember Me & Forgot Password Row */
        .options-row-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 0.5rem 0 1.5rem 0;
        }

        .check-container {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .purple-check-box {
            width: 18px;
            height: 18px;
            border-radius: 5px;
            border: 1.5px solid #cbc4e6;
            appearance: none;
            -webkit-appearance: none;
            outline: none;
            cursor: pointer;
            margin: 0;
            display: grid;
            place-content: center;
            background-color: #ffffff;
            transition: all 0.18s ease;
        }

        .purple-check-box:checked {
            background-color: var(--niko-purple-primary);
            border-color: var(--niko-purple-primary);
        }

        .purple-check-box:checked::before {
            content: '';
            width: 9px;
            height: 5px;
            border-left: 2px solid #ffffff;
            border-bottom: 2px solid #ffffff;
            transform: rotate(-45deg) translate(1px, -1px);
        }

        .check-label-text {
            margin-left: 9px;
            font-size: 0.88rem;
            color: #4b446a;
            user-select: none;
        }

        .forgot-link-btn {
            font-size: 0.85rem;
            font-weight: 600;
            color: #6352e8;
            text-decoration: none;
            transition: color 0.18s;
        }

        .forgot-link-btn:hover {
            color: #4b36d0;
            text-decoration: underline;
        }

        /* Sign In Button (SIGN IN ->) */
        .btn-submit-mockup {
            width: 100%;
            height: 50px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #6b5be8 0%, #5440de 100%);
            color: #ffffff;
            font-size: 0.95rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            box-shadow: 0 8px 24px rgba(99, 82, 232, 0.42);
            transition: all 0.22s ease-in-out;
        }

        .btn-submit-mockup:hover {
            background: linear-gradient(135deg, #7666ee 0%, #4a34d0 100%);
            box-shadow: 0 10px 28px rgba(99, 82, 232, 0.52);
            transform: translateY(-1px);
        }

        .btn-submit-mockup:active {
            transform: translateY(0);
            box-shadow: 0 4px 12px rgba(99, 82, 232, 0.3);
        }

        /* OR Divider */
        .or-divider-wrap {
            display: flex;
            align-items: center;
            margin: 1.5rem 0 1.25rem 0;
            color: #b0a9c8;
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .or-divider-wrap::before, .or-divider-wrap::after {
            content: '';
            flex: 1;
            height: 1px;
            background-color: #eeeaf8;
        }

        .or-divider-wrap span {
            padding: 0 14px;
        }

        /* Trust Security Pill Badge */
        .trust-security-pill {
            background-color: #f7f5fd;
            border: 1px solid #e7e2f8;
            border-radius: 12px;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .trust-icon-box {
            color: #6352e8;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .trust-title {
            font-size: 0.82rem;
            font-weight: 700;
            color: #2e2354;
            margin: 0;
            line-height: 1.2;
        }

        .trust-sub {
            font-size: 0.72rem;
            color: #79719c;
            margin: 2px 0 0 0;
        }

        /* Card Copyright Footer */
        .card-bottom-copy {
            margin-top: 1.6rem;
            text-align: center;
            font-size: 0.76rem;
            color: #928ca8;
        }

        /* Error Messages */
        .error-feedback-alert {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #e53e3e;
            font-size: 0.84rem;
            margin-top: 0.85rem;
            font-weight: 600;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .login-left-pane {
                width: 55%;
            }
            .login-right-pane {
                width: 45%;
                padding: 2rem 2.5rem;
            }
            .hero-main-title {
                font-size: 2.35rem;
            }
        }

        @media (max-width: 991.98px) {
            .login-master-container {
                flex-direction: column;
            }
            .login-left-pane {
                display: none;
            }
            .login-right-pane {
                width: 100%;
                min-height: 100vh;
                padding: 2.5rem 1.5rem;
            }
            .mockup-floating-card {
                padding: 2.2rem 1.8rem;
            }
        }
    </style>
</head>

<body>
    <div class="login-master-container">
        <!-- ============================================== -->
        <!-- LEFT COLUMN: Factory Hero Storytelling         -->
        <!-- ============================================== -->
        <div class="login-left-pane">
            <!-- Photorealistic Garment Factory Image -->
            <img 
                src="{{ url('assets/images/login.png') }}?v={{ filemtime(public_path('assets/images/login.png')) }}" 
                alt="NIKO Shirt Manufacturing Production Floor" 
                class="left-bg-image" 
            />

            <!-- Cinematic Vignette & Deep Lighting Gradient -->
            <div class="left-bg-gradient"></div>

            <!-- Handwritten Script Floating Slogan (From Mockup) -->
            <div class="script-quote-floating">
                Better Shirts,<br>Brighter Future
            </div>

            <!-- Foreground Content Overlay -->
            <div class="left-content-wrap">
                <!-- Top-Left Branding -->
                <div class="hero-brand-header">
                    <div class="hexagon-logo-badge">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2l8 4.5v11L12 22l-8-4.5v-11L12 2z"></path>
                            <path d="M12 7l4 2.5v5l-4 2.5-4-2.5v-5l4-2.5z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="hero-brand-name">NIKO</div>
                        <div class="hero-brand-tagline">GARMENTS ERP &bull; NACHIAS</div>
                    </div>
                </div>

                <!-- Mid-Bottom Hero Headline -->
                <div class="hero-headline-area">
                    <span class="fabric-pill-tag">From Fabric to Fashion</span>
                    <h1 class="hero-main-title">
                        Crafting<br>
                        <span class="gradient-word">Quality,</span><br>
                        Together.
                    </h1>
                    <p class="hero-main-desc">
                        One connected workspace for every stitch, every order, every delivery.
                    </p>
                </div>

                <!-- Bottom Workflow Metric Cards (4 Cards) -->
                <div class="bottom-workflow-glass">
                    <div class="workflow-cards-grid">
                        <!-- 1. Cutting -->
                        <div class="wf-card-item">
                            <div class="wf-card-header">
                                <span class="wf-icon-circle">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="6" cy="6" r="3"></circle>
                                        <circle cx="6" cy="18" r="3"></circle>
                                        <line x1="20" y1="4" x2="8.12" y2="15.88"></line>
                                        <line x1="14.47" y1="14.48" x2="20" y2="20"></line>
                                        <line x1="8.12" y1="8.12" x2="12" y2="12"></line>
                                    </svg>
                                </span>
                                <span class="wf-card-title">CUTTING</span>
                            </div>
                            <p class="wf-card-sub">Smart Planning<br>Less Waste</p>
                        </div>

                        <!-- 2. Production -->
                        <div class="wf-card-item">
                            <div class="wf-card-header">
                                <span class="wf-icon-circle">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20.38 3.46L16 2a4 4 0 0 0-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23z"></path>
                                    </svg>
                                </span>
                                <span class="wf-card-title">PRODUCTION</span>
                            </div>
                            <p class="wf-card-sub">On Time<br>Every Time</p>
                        </div>

                        <!-- 3. Quality -->
                        <div class="wf-card-item">
                            <div class="wf-card-header">
                                <span class="wf-icon-circle">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                    </svg>
                                </span>
                                <span class="wf-card-title">QUALITY</span>
                            </div>
                            <p class="wf-card-sub">Standards<br>You Can Trust</p>
                        </div>

                        <!-- 4. Dispatch -->
                        <div class="wf-card-item">
                            <div class="wf-card-header">
                                <span class="wf-icon-circle">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="1" y="3" width="15" height="13"></rect>
                                        <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                                        <circle cx="5.5" cy="18.5" r="2.5"></circle>
                                        <circle cx="18.5" cy="18.5" r="2.5"></circle>
                                    </svg>
                                </span>
                                <span class="wf-card-title">DISPATCH</span>
                            </div>
                            <p class="wf-card-sub">On Track<br>To Your Door</p>
                        </div>
                    </div>

                    <!-- Bottom Subline -->
                    <div class="bottom-company-subline">
                        NACHIAS FASHION PRIVATE LIMITED
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- RIGHT COLUMN: Luxury Satin + Floating Card     -->
        <!-- ============================================== -->
        <div class="login-right-pane">
            <!-- Satin Silk Wave Layers -->
            <div class="satin-wave-top"></div>
            <div class="satin-wave-bottom"></div>

            <!-- Top-Right Secure Workspace Badge -->
            <div class="secure-badge-corner">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                SECURE WORKSPACE
                <span class="secure-green-dot"></span>
            </div>

            <!-- Decorative Button Accent (Bottom Right) -->
            <div class="satin-button-decor"></div>

            <!-- FLOATING LOGIN CARD -->
            <div class="mockup-floating-card">
                <!-- Centered Logo Block -->
                <div class="card-brand-center">
                    <div class="card-hexagon-emblem">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#5846e0" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2l8 4.5v11L12 22l-8-4.5v-11L12 2z"></path>
                            <path d="M12 7l4 2.5v5l-4 2.5-4-2.5v-5l4-2.5z"></path>
                        </svg>
                    </div>
                    <div class="card-niko-title">NIKO</div>
                    <div class="card-niko-subtitle">GARMENTS ERP &bull; NACHIAS</div>
                </div>

                <!-- Welcome Text -->
                <h2 class="card-heading-title">Welcome <span class="purple-back">back.</span></h2>
                <p class="card-heading-desc">Sign in to your manufacturing workspace.</p>

                <!-- Flash Messages -->
                @include('flash_messages')

                <!-- Authentication Form -->
                <form id="formAuthentication" action="{{ url('login') }}" method="POST" autocomplete="off">
                    @csrf

                    <!-- Email Field -->
                    <div class="field-wrap-box">
                        <span class="field-icon-left">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                            </svg>
                        </span>
                        <input 
                            type="text" 
                            class="field-input-control @error('email') is-invalid @enderror" 
                            id="email" 
                            name="email" 
                            placeholder="Email Address" 
                            value="{{ old('email', Cookie::get('email') ?? '') }}" 
                            required 
                        />
                    </div>

                    <!-- Password Field with Lock Prefix & Eye Toggle Suffix -->
                    <div class="field-wrap-box">
                        <span class="field-icon-left">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </span>
                        <input 
                            type="password" 
                            class="field-input-control @error('password') is-invalid @enderror" 
                            id="form-alignment-password" 
                            name="password" 
                            placeholder="Password" 
                            value="{{ old('password', Cookie::get('password') ?? '') }}" 
                            onkeypress="return event.charCode !== 32" 
                            required 
                        />
                        <span class="field-icon-eye" id="pwdToggleBtn" role="button" aria-label="Toggle password visibility">
                            <svg id="iconEyeSvg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path>
                                <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"></path>
                                <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"></path>
                                <line x1="2" y1="2" x2="22" y2="22"></line>
                            </svg>
                        </span>
                    </div>

                    <!-- Remember Me & Forgot Password Row -->
                    <div class="options-row-wrap">
                        <label class="check-container" for="remember-me">
                            <input 
                                type="checkbox" 
                                class="purple-check-box" 
                                name="remember" 
                                id="remember-me" 
                                value="1" 
                                {{ Cookie::get('remember') ? 'checked' : '' }} 
                            />
                            <span class="check-label-text">Remember Me</span>
                        </label>
                        <a href="javascript:void(0);" class="forgot-link-btn">Forgot password?</a>
                    </div>

                    <!-- Sign In Purple Gradient Button -->
                    <button class="btn-submit-mockup" type="submit" id="btnSignIn">
                        <span>SIGN IN</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </button>

                    <!-- Validation Errors -->
                    @if ($errors->has('email') || $errors->has('password'))
                        <div class="error-feedback-alert">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            <span>{{ $errors->first('email') ?: $errors->first('password') }}</span>
                        </div>
                    @endif
                </form>

                <!-- OR Divider -->
                <div class="or-divider-wrap">
                    <span>OR</span>
                </div>

                <!-- Trust / Security Pill -->
                <div class="trust-security-pill">
                    <div class="trust-icon-box">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            <path d="m9 12 2 2 4-4"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="trust-title">Your data is safe with us</p>
                        <p class="trust-sub">Secure &bull; Trusted &bull; Confidential</p>
                    </div>
                </div>

                <!-- Footer Copyright -->
                <div class="card-bottom-copy">
                    Nachias Fashion Private Limited &copy; {{ date('Y') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ url('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ url('assets/js/bootstrap.js') }}"></script>

    <!-- Reliable Password Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var toggleBtn = document.getElementById('pwdToggleBtn');
            var pwdInput = document.getElementById('form-alignment-password');
            var iconEye = document.getElementById('iconEyeSvg');

            var eyeSlashSvg = '<path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"></path><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"></path><line x1="2" y1="2" x2="22" y2="22"></line>';
            var eyeOpenSvg = '<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="12" r="3"></circle>';

            if (toggleBtn && pwdInput && iconEye) {
                toggleBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    if (pwdInput.type === 'password') {
                        pwdInput.type = 'text';
                        iconEye.innerHTML = eyeOpenSvg;
                    } else {
                        pwdInput.type = 'password';
                        iconEye.innerHTML = eyeSlashSvg;
                    }
                });
            }
        });
    </script>
</body>
</html>