<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nachias Garments ERP &bull; Login</title>
    <link rel="icon" type="image/jpeg" href="{{ url('assets/images/fav.jpeg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Public+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --nachias-purple: #5c38ee;
            --nachias-purple-dark: #4524c7;
            --nachias-purple-soft: #f4f2fe;
            --nachias-purple-border: #eae4f8;
            --nachias-navy-title: #130b35;
            --nachias-navy-text: #2a2250;
            --nachias-text-muted: #6a6486;
            --nachias-input-border: #e2ddf2;
            --nachias-page-bg: #f4f6fc;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            width: 100%;
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--nachias-page-bg);
            background-image: 
                radial-gradient(circle at 10% 15%, rgba(225, 215, 255, 0.45) 0%, transparent 45%),
                radial-gradient(circle at 88% 25%, rgba(210, 225, 255, 0.45) 0%, transparent 40%),
                radial-gradient(circle at 50% 90%, rgba(240, 235, 255, 0.5) 0%, transparent 50%);
            color: var(--nachias-navy-title);
            overflow-x: hidden;
        }

        /* Master Full-Height Layout */
        .page-layout-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 1.5rem 3rem 1.25rem;
            position: relative;
        }

        /* ============================================== */
        /* TOP HEADER                                     */
        /* ============================================== */
        .top-navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            max-width: 1520px;
            margin: 0 auto 1.2rem auto;
        }

        .brand-header-link {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .header-shield-emblem {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: #000000;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            flex-shrink: 0;
        }

        .header-shield-emblem svg {
            width: 24px;
            height: 24px;
        }

        .header-brand-texts {
            display: flex;
            flex-direction: column;
        }

        .header-brand-name {
            font-size: 1.95rem;
            font-weight: 800;
            color: var(--nachias-navy-title);
            line-height: 1;
            letter-spacing: -0.6px;
        }

        .header-brand-sub {
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 2.2px;
            color: #1e1346;
            text-transform: uppercase;
            margin-top: 4px;
        }

        .top-nav-right {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            font-size: 0.88rem;
            color: var(--nachias-text-muted);
        }

        .top-slogan-text {
            font-weight: 500;
            color: #554e73;
        }

        .nav-vertical-pipe {
            width: 1px;
            height: 18px;
            background: #d8d3e8;
        }

        .lang-selector-pill {
            display: flex;
            align-items: center;
            gap: 5px;
            font-weight: 600;
            color: var(--nachias-navy-title);
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 6px;
            transition: background 0.15s;
        }

        .lang-selector-pill:hover {
            background: rgba(92, 56, 238, 0.08);
        }

        /* ============================================== */
        /* MAIN 3-COLUMN HERO COMPOSITION                 */
        /* ============================================== */
        .hero-layout-grid {
            width: 100%;
            max-width: 1520px;
            margin: auto auto;
            display: grid;
            grid-template-columns: 1.15fr 0.95fr 0.9fr;
            gap: 2rem;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        /* ---------------------------------------------- */
        /* COLUMN 1: Story, Workflow Pills & Factory      */
        /* ---------------------------------------------- */
        .hero-left-panel {
            display: flex;
            flex-direction: column;
            padding-right: 0.5rem;
        }

        .category-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            color: var(--nachias-purple);
            margin-bottom: 0.85rem;
        }

        .category-eyebrow::before {
            content: '';
            width: 22px;
            height: 2.5px;
            background: var(--nachias-purple);
            border-radius: 2px;
        }

        .hero-main-heading {
            font-size: 2.9rem;
            font-weight: 800;
            line-height: 1.12;
            color: var(--nachias-navy-title);
            letter-spacing: -1px;
            margin-bottom: 0.85rem;
        }

        .headline-purple-highlight {
            color: var(--nachias-purple);
            display: inline-block;
        }

        .hero-sub-description {
            font-size: 0.94rem;
            line-height: 1.6;
            color: var(--nachias-text-muted);
            max-width: 480px;
            margin-bottom: 1.4rem;
        }

        /* 4 Feature Columns */
        .features-grid-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.65rem;
            margin-bottom: 1.4rem;
        }

        .feature-card-item {
            background: #ffffff;
            border: 1px solid var(--nachias-purple-border);
            border-radius: 12px;
            padding: 0.75rem 0.65rem;
            display: flex;
            flex-direction: column;
            gap: 3px;
            box-shadow: 0 4px 14px rgba(35, 18, 90, 0.03);
            transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
        }

        .feature-card-item:hover {
            transform: translateY(-2px);
            border-color: var(--nachias-purple);
            box-shadow: 0 8px 20px rgba(92, 56, 238, 0.1);
        }

        .feature-icon-row {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--nachias-purple);
            margin-bottom: 2px;
        }

        .feature-icon-row svg {
            width: 17px;
            height: 17px;
            flex-shrink: 0;
        }

        .feature-title-text {
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--nachias-navy-title);
        }

        .feature-sub-desc {
            font-size: 0.68rem;
            color: var(--nachias-text-muted);
            line-height: 1.25;
        }

        /* Standalone Women Stitching Factory Image Card */
        .factory-stitching-wrap {
            position: relative;
            width: 100%;
            border-radius: 18px 80px 18px 18px;
            overflow: hidden;
            box-shadow: 0 16px 36px rgba(25, 15, 60, 0.08);
            border: 1px solid rgba(225, 218, 245, 0.6);
            background: #ffffff;
        }

        .factory-stitching-img {
            width: 100%;
            height: auto;
            display: block;
            border-radius: 18px 80px 18px 18px;
            transition: transform 0.35s ease;
        }

        .factory-stitching-wrap:hover .factory-stitching-img {
            transform: scale(1.02);
        }

        /* ---------------------------------------------- */
        /* COLUMN 2: Elevated Central Login Card          */
        /* ---------------------------------------------- */
        .hero-center-panel {
            display: flex;
            justify-content: center;
        }

        .central-login-card {
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border-radius: 24px;
            padding: 2.25rem 2.15rem 1.85rem;
            box-shadow: 0 24px 60px rgba(45, 26, 120, 0.08), 0 2px 10px rgba(0, 0, 0, 0.02);
            border: 1px solid var(--nachias-purple-border);
            position: relative;
            z-index: 10;
        }

        .card-brand-top {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 1.35rem;
        }

        .card-shield-black {
            width: 42px;
            height: 42px;
            border-radius: 11px;
            background: #000000;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            flex-shrink: 0;
            box-shadow: 0 3px 8px rgba(0,0,0,0.15);
        }

        .card-shield-black svg {
            width: 22px;
            height: 22px;
        }

        .card-brand-texts {
            display: flex;
            flex-direction: column;
        }

        .card-brand-title-purple {
            font-size: 1.85rem;
            font-weight: 800;
            color: var(--nachias-purple);
            line-height: 1;
            letter-spacing: -0.5px;
        }

        .card-brand-sub-caps {
            font-size: 0.58rem;
            font-weight: 700;
            letter-spacing: 1.8px;
            color: #1e1346;
            text-transform: uppercase;
            margin-top: 3px;
        }

        .card-welcome-header {
            text-align: left;
            margin-bottom: 1.25rem;
        }

        .card-welcome-title {
            font-size: 1.55rem;
            font-weight: 800;
            color: var(--nachias-navy-title);
            margin-bottom: 0.2rem;
            letter-spacing: -0.5px;
        }

        .card-welcome-sub {
            font-size: 0.85rem;
            color: var(--nachias-text-muted);
        }

        /* Form Inputs */
        .form-input-group {
            position: relative;
            margin-bottom: 0.95rem;
        }

        .input-icon-prefix {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9d96b8;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            z-index: 2;
        }

        .input-icon-prefix svg {
            width: 17px;
            height: 17px;
        }

        .custom-text-input {
            width: 100%;
            height: 48px;
            padding: 0 16px 0 44px;
            font-family: inherit;
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--nachias-navy-title);
            background: #ffffff;
            border: 1.5px solid var(--nachias-input-border);
            border-radius: 12px;
            outline: none;
            transition: all 0.18s ease;
        }

        .custom-text-input::placeholder {
            color: #a49ebf;
            font-weight: 400;
        }

        .custom-text-input:focus {
            border-color: var(--nachias-purple);
            box-shadow: 0 0 0 3.5px rgba(92, 56, 238, 0.12);
        }

        .custom-text-input.is-invalid {
            border-color: #ea5455;
            background-color: #fff9f9;
        }

        .password-toggle-suffix {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9d96b8;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
            transition: color 0.15s;
        }

        .password-toggle-suffix:hover {
            color: var(--nachias-purple);
        }

        .password-toggle-suffix svg {
            width: 18px;
            height: 18px;
        }

        /* Options Row */
        .form-options-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 0.35rem 0 1.2rem 0;
            font-size: 0.84rem;
        }

        .checkbox-container-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            user-select: none;
            color: #3f3760;
            font-weight: 500;
        }

        .purple-checkbox-control {
            width: 18px;
            height: 18px;
            border-radius: 5px;
            border: 1.5px solid #cac2e6;
            appearance: none;
            -webkit-appearance: none;
            outline: none;
            cursor: pointer;
            margin: 0;
            display: grid;
            place-content: center;
            background: #ffffff;
            transition: all 0.15s;
        }

        .purple-checkbox-control:checked {
            background-color: var(--nachias-purple);
            border-color: var(--nachias-purple);
        }

        .purple-checkbox-control:checked::before {
            content: '';
            width: 9px;
            height: 5px;
            border-left: 2px solid #ffffff;
            border-bottom: 2px solid #ffffff;
            transform: rotate(-45deg) translate(1px, -1px);
        }

        .forgot-link-anchor {
            font-weight: 600;
            color: var(--nachias-purple);
            text-decoration: none;
            transition: color 0.15s;
        }

        .forgot-link-anchor:hover {
            color: var(--nachias-purple-dark);
            text-decoration: underline;
        }

        /* Sign In Button */
        .btn-submit-signin {
            width: 100%;
            height: 48px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #6438ee 0%, #4a22db 100%);
            color: #ffffff;
            font-family: inherit;
            font-size: 0.95rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            box-shadow: 0 8px 22px rgba(92, 56, 238, 0.35);
            transition: all 0.2s ease;
        }

        .btn-submit-signin:hover {
            background: linear-gradient(135deg, #7047f5 0%, #3f18cb 100%);
            box-shadow: 0 10px 26px rgba(92, 56, 238, 0.45);
            transform: translateY(-1px);
        }

        .btn-submit-signin:active {
            transform: translateY(0);
            box-shadow: 0 4px 12px rgba(92, 56, 238, 0.25);
        }

        .btn-submit-signin svg {
            width: 16px;
            height: 16px;
            stroke-width: 2.4;
            transition: transform 0.18s ease;
        }

        .btn-submit-signin:hover svg {
            transform: translateX(3px);
        }

        /* OR Divider */
        .or-divider-box {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.15rem 0;
            color: #a7a0c0;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .or-divider-box::before,
        .or-divider-box::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e8e3f4;
        }

        .or-divider-box span {
            padding: 0 12px;
        }

        /* Bottom Security Panel */
        .security-trust-box {
            background: var(--nachias-purple-soft);
            border: 1px solid var(--nachias-purple-border);
            border-radius: 14px;
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .trust-shield-icon {
            color: var(--nachias-purple);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .trust-shield-icon svg {
            width: 24px;
            height: 24px;
        }

        .trust-text-stack {
            display: flex;
            flex-direction: column;
            text-align: left;
            line-height: 1.3;
        }

        .trust-heading {
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--nachias-navy-title);
        }

        .trust-subtext {
            font-size: 0.7rem;
            color: var(--nachias-text-muted);
            margin-top: 1px;
        }

        /* Error Feedback */
        .error-alert-badge {
            background: #fff5f5;
            border: 1px solid #ffd1d1;
            color: #d32f2f;
            border-radius: 10px;
            padding: 8px 12px;
            font-size: 0.8rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 0.75rem;
        }

        /* ---------------------------------------------- */
        /* COLUMN 3: Standalone Finished Shirt Showcase   */
        /* ---------------------------------------------- */
        .hero-right-panel {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
        }

        .shirt-showcase-container {
            position: relative;
            width: 100%;
            max-width: 360px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .shirt-product-img {
            width: 100%;
            height: auto;
            max-height: 540px;
            object-fit: contain;
            filter: drop-shadow(0 20px 40px rgba(35, 20, 80, 0.12));
            display: block;
            transition: transform 0.3s ease;
        }

        .shirt-showcase-container:hover .shirt-product-img {
            transform: translateY(-4px) scale(1.01);
        }

        /* ============================================== */
        /* FOOTER                                         */
        /* ============================================== */
        .page-footer-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            max-width: 1520px;
            margin: 1.5rem auto 0 auto;
            padding: 0.6rem 0;
            border-top: 1px solid rgba(215, 208, 235, 0.5);
            font-size: 0.75rem;
            color: var(--nachias-text-muted);
        }

        .footer-company-text {
            font-weight: 700;
            letter-spacing: 1.8px;
            text-transform: uppercase;
            color: #554e73;
        }

        .footer-powered-by {
            display: flex;
            align-items: center;
            gap: 7px;
            font-weight: 500;
        }

        .footer-shield-icon {
            width: 16px;
            height: 16px;
            color: var(--nachias-purple);
        }

        /* ============================================== */
        /* RESPONSIVE BREAKPOINTS                         */
        /* ============================================== */
        @media (max-width: 1280px) {
            .page-layout-wrapper {
                padding: 1.25rem 2rem 1rem;
            }
            .hero-layout-grid {
                gap: 1.5rem;
                grid-template-columns: 1.1fr 1fr 0.85fr;
            }
            .hero-main-heading {
                font-size: 2.45rem;
            }
            .features-grid-row {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 991.98px) {
            .hero-layout-grid {
                grid-template-columns: 1fr;
                gap: 2.5rem;
                margin: 1rem auto;
            }
            .hero-right-panel {
                display: none;
            }
            .hero-left-panel {
                text-align: center;
                align-items: center;
                padding-right: 0;
            }
            .hero-sub-description {
                max-width: 100%;
            }
            .features-grid-row {
                max-width: 500px;
                width: 100%;
                grid-template-columns: repeat(4, 1fr);
            }
            .factory-stitching-wrap {
                max-width: 500px;
                border-radius: 18px;
            }
            .factory-stitching-img {
                border-radius: 18px;
            }
        }

        @media (max-width: 640px) {
            .page-layout-wrapper {
                padding: 1rem 1.25rem;
            }
            .top-slogan-text, .nav-vertical-pipe {
                display: none;
            }
            .hero-main-heading {
                font-size: 2rem;
            }
            .features-grid-row {
                grid-template-columns: repeat(2, 1fr);
            }
            .central-login-card {
                padding: 1.75rem 1.35rem;
            }
            .page-footer-bar {
                flex-direction: column;
                gap: 6px;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <div class="page-layout-wrapper">
        <!-- ============================================== -->
        <!-- TOP HEADER (Real Direct HTML Words & Logo)    -->
        <!-- ============================================== -->
        <header class="top-navbar">
            <a href="{{ url('/') }}" class="brand-header-link">
                <div class="header-shield-emblem">
                    <!-- Black Shield Spade Logo SVG -->
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        <path d="M12 8v8"></path>
                        <path d="M9.5 12l2.5-3 2.5 3"></path>
                        <circle cx="12" cy="17" r="1" fill="currentColor"></circle>
                    </svg>
                </div>
                <div class="header-brand-texts">
                    <span class="header-brand-name">Nachias</span>
                    <span class="header-brand-sub">GARMENTS ERP &bull; MANUFACTURING</span>
                </div>
            </a>

            <div class="top-nav-right">
                <span class="top-slogan-text">Build Better. Manufacture Smarter.</span>
            </div>
        </header>

        <!-- ============================================== -->
        <!-- MAIN 3-COLUMN HERO COMPOSITION                 -->
        <!-- ============================================== -->
        <main class="hero-layout-grid">
            <!-- ------------------------------------------ -->
            <!-- COLUMN 1: Story, Workflow Pills & Factory   -->
            <!-- ------------------------------------------ -->
            <section class="hero-left-panel">
                <div class="category-eyebrow">GARMENT MANUFACTURING ERP</div>
                <h1 class="hero-main-heading">
                    From Fabric<br>
                    <span class="headline-purple-highlight">to Finished Shirts.</span>
                </h1>
                <p class="hero-sub-description">
                    Streamline your production, track every order, and deliver quality &mdash; all in one powerful platform.
                </p>

                <!-- Four Feature Columns (Real Direct HTML) -->
                <div class="features-grid-row">
                    <!-- 1. Cutting -->
                    <div class="feature-card-item">
                        <div class="feature-icon-row">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="6" cy="6" r="3"></circle>
                                <circle cx="6" cy="18" r="3"></circle>
                                <line x1="20" y1="4" x2="8.12" y2="15.88"></line>
                                <line x1="14.47" y1="14.48" x2="20" y2="20"></line>
                                <line x1="8.12" y1="8.12" x2="12" y2="12"></line>
                            </svg>
                            <span class="feature-title-text">Cutting</span>
                        </div>
                        <span class="feature-sub-desc">Plan &amp; Track</span>
                    </div>

                    <!-- 2. Production -->
                    <div class="feature-card-item">
                        <div class="feature-icon-row">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 7h18v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7z"></path>
                                <path d="M16 3v4"></path>
                                <path d="M8 3v4"></path>
                                <circle cx="12" cy="14" r="2.5"></circle>
                            </svg>
                            <span class="feature-title-text">Production</span>
                        </div>
                        <span class="feature-sub-desc">Monitor Progress</span>
                    </div>

                    <!-- 3. Quality -->
                    <div class="feature-card-item">
                        <div class="feature-icon-row">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                <polyline points="9 12 11 14 15 10"></polyline>
                            </svg>
                            <span class="feature-title-text">Quality</span>
                        </div>
                        <span class="feature-sub-desc">Ensure Standards</span>
                    </div>

                    <!-- 4. Dispatch -->
                    <div class="feature-card-item">
                        <div class="feature-icon-row">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="1" y="3" width="15" height="13"></rect>
                                <polygon points="16 8 20 8 23 11 23 16 16 16 8"></polygon>
                                <circle cx="5.5" cy="18.5" r="2.5"></circle>
                                <circle cx="18.5" cy="18.5" r="2.5"></circle>
                            </svg>
                            <span class="feature-title-text">Dispatch</span>
                        </div>
                        <span class="feature-sub-desc">On Time Delivery</span>
                    </div>
                </div>

                <!-- Standalone Women Stitching Factory Image with Sweeping Curve & Badge -->
                <div class="factory-stitching-wrap">
                    <img 
                        src="{{ url('assets/images/nachias_factory_isolated.jpg') }}?v={{ time() }}" 
                        alt="Women stitching in shirt manufacturing factory" 
                        class="factory-stitching-img"
                    />
                </div>
            </section>

            <!-- ------------------------------------------ -->
            <!-- COLUMN 2: Central Interactive Login Card   -->
            <!-- ------------------------------------------ -->
            <section class="hero-center-panel">
                <div class="central-login-card">
                    <!-- Brand Centered Logo inside Card -->
                    <div class="card-brand-top">
                        <div class="card-shield-black">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                <path d="M12 8v8"></path>
                                <path d="M9.5 12l2.5-3 2.5 3"></path>
                                <circle cx="12" cy="17" r="1" fill="currentColor"></circle>
                            </svg>
                        </div>
                        <div class="card-brand-texts">
                            <span class="card-brand-title-purple">Niko</span>
                            <span class="card-brand-sub-caps">GARMENTS ERP &bull; MANUFACTURING</span>
                        </div>
                    </div>

                    <!-- Welcome Heading -->
                    <div class="card-welcome-header">
                        <h2 class="card-welcome-title">Welcome Back</h2>
                        <p class="card-welcome-sub">Sign in to your manufacturing workspace.</p>
                    </div>

                    <!-- Flash Messages -->
                    @include('flash_messages')

                    <!-- Authentication Form -->
                    <form id="formAuthentication" action="{{ url('login') }}" method="POST" autocomplete="off">
                        @csrf

                        <!-- Email Address Input -->
                        <div class="form-input-group">
                            <span class="input-icon-prefix">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                                </svg>
                            </span>
                            <input 
                                type="text" 
                                class="custom-text-input @error('email') is-invalid @enderror" 
                                id="email" 
                                name="email" 
                                placeholder="Email Address" 
                                value="{{ old('email', Cookie::get('email') ?? '') }}" 
                                required 
                            />
                        </div>

                        <!-- Password Input with Toggle Eye -->
                        <div class="form-input-group">
                            <span class="input-icon-prefix">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                            </span>
                            <input 
                                type="password" 
                                class="custom-text-input @error('password') is-invalid @enderror" 
                                id="form-alignment-password" 
                                name="password" 
                                placeholder="Password" 
                                value="{{ old('password', Cookie::get('password') ?? '') }}" 
                                onkeypress="return event.charCode !== 32" 
                                required 
                            />
                            <span class="password-toggle-suffix" id="pwdToggleBtn" role="button" aria-label="Toggle password visibility">
                                <svg id="iconEyeSvg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path>
                                    <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"></path>
                                    <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"></path>
                                    <line x1="2" y1="2" x2="22" y2="22"></line>
                                </svg>
                            </span>
                        </div>

                        <!-- Options Row: Remember Me & Forgot Password -->
                        <div class="form-options-bar">
                            <label class="checkbox-container-label" for="remember-me">
                                <input 
                                    type="checkbox" 
                                    class="purple-checkbox-control" 
                                    name="remember" 
                                    id="remember-me" 
                                    value="1" 
                                    {{ Cookie::get('remember') ? 'checked' : '' }} 
                                />
                                <span>Remember Me</span>
                            </label>
                            <a href="javascript:void(0);" class="forgot-link-anchor">Forgot Password?</a>
                        </div>

                        <!-- Sign In Button -->
                        <button class="btn-submit-signin" type="submit" id="btnSignIn">
                            <span>Sign In</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </button>

                        <!-- Validation Alert -->
                        @if ($errors->has('email') || $errors->has('password'))
                            <div class="error-alert-badge">
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
                    <div class="or-divider-box">
                        <span>OR</span>
                    </div>

                    <!-- Trust Security Badge -->
                    <div class="security-trust-box">
                        <div class="trust-shield-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                <polyline points="9 12 11 14 15 10"></polyline>
                            </svg>
                        </div>
                        <div class="trust-text-stack">
                            <span class="trust-heading">Your data is safe with us</span>
                            <span class="trust-subtext">Secure &bull; Trusted &bull; Confidential</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ------------------------------------------ -->
            <!-- COLUMN 3: Standalone Finished Shirt Image  -->
            <!-- ------------------------------------------ -->
            <aside class="hero-right-panel">
                <div class="shirt-showcase-container">
                    <img 
                        src="{{ url('assets/images/nachias_shirt_clean.png') }}?v={{ time() }}" 
                        alt="Finished Casino Formal Shirts - Nachias Fashion" 
                        class="shirt-product-img"
                    />
                </div>
            </aside>
        </main>

        <!-- ============================================== -->
        <!-- FOOTER (Real Direct HTML Words)                -->
        <!-- ============================================== -->
        <footer class="page-footer-bar">
            <span class="footer-company-text">NACHIAS FASHION PRIVATE LIMITED</span>
            <div class="footer-powered-by">
                <svg class="footer-shield-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    <path d="M12 8v8"></path>
                    <path d="M9.5 12l2.5-3 2.5 3"></path>
                </svg>
                <span>Powered by Nachias Garments ERP</span>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="{{ url('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ url('assets/js/bootstrap.js') }}"></script>

    <!-- Reliable Password Toggle -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var toggleBtn = document.getElementById('pwdToggleBtn');
            var pwdInput = document.getElementById('form-alignment-password');
            var iconEye = document.getElementById('iconEyeSvg');

            var eyeSlashSvg = '<path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"></path><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"></path><line x1="2" y1="2" x2="22" y2="22"></line>';
            var eyeOpenSvg = '<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path><circle cx="12" cy="17" r="3"></circle>';

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