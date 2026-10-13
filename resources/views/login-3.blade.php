<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NIKO Garments ERP &bull; Login</title>
    <link rel="icon" type="image/jpeg" href="{{ url('assets/images/fav.jpeg') }}">
    <link rel="stylesheet" href="{{ url('assets/css/core.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/demo.css') }}">
    <link rel="stylesheet" href="{{ url('assets/fonts/iconify-icons.css') }}">
    <link rel="stylesheet" href="{{ url('assets/css/node-waves.css') }}">

    <style>
        :root {
            --niko-purple-primary: #6352e8;
            --niko-purple-hover: #5240de;
            --niko-purple-dark: #372485;
            --niko-purple-light: #7b6cf0;
            --niko-bg-card: #ffffff;
            --niko-text-dark: #1b1338;
            --niko-text-muted: #746f8c;
            --niko-input-bg: #f8f7fd;
            --niko-input-border: #e6e2f5;
        }

        * {
            box-sizing: border-box;
        }

        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family: 'Public Sans', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #1a1236;
            overflow-x: hidden;
        }

        /* Full-screen Layout Container */
        .login-layout {
            position: relative;
            width: 100%;
            min-height: 100vh;
            display: flex;
            background-color: #1a1236;
            overflow: hidden;
        }

        /* Full Background Image Layer Spanning Entire Viewport */
        .login-bg-layer {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            overflow: hidden;
        }

        .login-bg-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center center;
            display: block;
            filter: contrast(1.02) saturate(1.05);
        }

        /* Soft Cinematic Vignette across image */
        .login-bg-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, 
                rgba(12, 7, 28, 0.55) 0%, 
                rgba(12, 7, 28, 0.15) 30%, 
                rgba(18, 11, 40, 0.25) 55%, 
                rgba(35, 20, 80, 0.45) 75%, 
                rgba(48, 28, 110, 0.6) 100%),
                linear-gradient(180deg, 
                rgba(12, 7, 28, 0.3) 0%, 
                rgba(12, 7, 28, 0) 45%, 
                rgba(12, 7, 28, 0.75) 80%, 
                rgba(12, 7, 28, 0.95) 100%);
            z-index: 2;
            pointer-events: none;
        }

        /* Right Ambient Atmospheric Violet Glow (Translucent so factory photo shows behind card) */
        .login-right-ambient {
            position: absolute;
            top: 0;
            right: 0;
            width: 52%;
            height: 100%;
            background: linear-gradient(90deg, 
                rgba(20, 10, 48, 0) 0%, 
                rgba(35, 18, 80, 0.25) 25%, 
                rgba(65, 38, 145, 0.45) 60%, 
                rgba(80, 48, 175, 0.65) 100%);
            pointer-events: none;
            z-index: 2;
        }

        /* Translucent Diagonal Geometric Ribbons / Glass Facets matching Image 2 */
        .facet-ribbon-primary {
            position: absolute;
            top: -12%;
            right: -8%;
            width: 42%;
            height: 128%;
            background: linear-gradient(135deg, 
                rgba(145, 115, 255, 0.45) 0%, 
                rgba(105, 75, 215, 0.62) 40%, 
                rgba(65, 40, 160, 0.75) 100%);
            clip-path: polygon(36% 0%, 100% 0%, 100% 100%, 0% 100%);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-left: 1px solid rgba(255, 255, 255, 0.35);
            box-shadow: -15px 0 40px rgba(18, 8, 48, 0.35);
            pointer-events: none;
            z-index: 3;
        }

        .facet-ribbon-accent {
            position: absolute;
            top: -8%;
            right: -4%;
            width: 25%;
            height: 118%;
            background: linear-gradient(135deg, 
                rgba(195, 170, 255, 0.4) 0%, 
                rgba(125, 95, 235, 0.55) 50%, 
                rgba(75, 45, 170, 0.7) 100%);
            clip-path: polygon(52% 0%, 100% 0%, 100% 100%, 0% 100%);
            border-left: 1px solid rgba(255, 255, 255, 0.45);
            pointer-events: none;
            z-index: 3;
        }

        .facet-ribbon-bottom {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 28%;
            height: 38%;
            background: linear-gradient(135deg, 
                rgba(155, 128, 255, 0.4) 0%, 
                rgba(85, 55, 185, 0.65) 100%);
            clip-path: polygon(0% 100%, 100% 15%, 100% 100%);
            pointer-events: none;
            z-index: 3;
        }

        /* Left Side Content (Brand Badge + Bottom Headlines + Workflow) */
        .login-left-content {
            position: relative;
            z-index: 5;
            width: 56%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 3rem 4rem 3.5rem 4rem;
            pointer-events: auto;
        }

        /* Top-Left Glass Brand Badge */
        .brand-badge-top {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 9px 18px 9px 12px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.94);
            border: 1px solid rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.16);
            width: fit-content;
        }

        .brand-badge-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: #ffffff;
            border: 1px solid #e5e0f8;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .brand-badge-icon img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .brand-badge-name {
            font-size: 1.25rem;
            font-weight: 800;
            letter-spacing: 2.2px;
            color: #43329b;
            line-height: 1;
            font-family: 'Public Sans', system-ui, sans-serif;
        }

        .brand-badge-sub {
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 1.4px;
            color: #79719c;
            text-transform: uppercase;
            margin-top: 2px;
        }

        /* Bottom Left Hero Caption & Workflow */
        .hero-caption-wrap {
            max-width: 660px;
        }

        .hero-title {
            font-size: 2.35rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.02em;
            line-height: 1.2;
            margin: 0 0 0.65rem 0;
            text-shadow: 0 3px 12px rgba(0, 0, 0, 0.45);
        }

        .hero-title .highlight-stitch {
            color: #b5a4ff;
        }

        .hero-desc {
            font-size: 0.98rem;
            line-height: 1.55;
            color: rgba(240, 236, 255, 0.9);
            margin: 0 0 1.6rem 0;
            max-width: 580px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.35);
        }

        /* Workflow Pills Row with Arrows */
        .workflow-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .workflow-step {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .workflow-icon-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.16);
            border: 1px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .workflow-label {
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 1.4px;
            color: #ffffff;
            text-transform: uppercase;
        }

        .workflow-arrow {
            color: rgba(200, 185, 255, 0.6);
            font-size: 0.95rem;
            user-select: none;
            margin: 0 2px;
        }

        /* Right Side Floating Login Card Container */
        .login-right-content {
            position: relative;
            z-index: 5;
            width: 44%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 3.5rem;
        }

        /* Floating Clean White Card */
        .floating-card {
            width: 100%;
            max-width: 430px;
            background: var(--niko-bg-card);
            border-radius: 22px;
            padding: 2.75rem 2.4rem;
            box-shadow: 0 24px 65px rgba(18, 10, 48, 0.4), 0 4px 16px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.9);
            position: relative;
        }

        /* Card Logo Header */
        .card-brand-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 1.75rem;
        }

        .card-brand-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: #ffffff;
            border: 1.5px solid var(--niko-input-border);
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 14px rgba(99, 82, 232, 0.12);
        }

        .card-brand-icon img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .card-brand-name {
            font-size: 1.55rem;
            font-weight: 800;
            letter-spacing: 2.8px;
            color: #43329b;
            line-height: 1;
            font-family: 'Public Sans', system-ui, sans-serif;
        }

        .card-brand-sub {
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 1.6px;
            color: #79719c;
            text-transform: uppercase;
            margin-top: 3px;
        }

        /* Welcome Text */
        .card-title {
            font-size: 1.45rem;
            font-weight: 700;
            color: var(--niko-text-dark);
            margin: 0 0 0.35rem 0;
            letter-spacing: -0.015em;
        }

        .card-subtitle {
            font-size: 0.88rem;
            color: var(--niko-text-muted);
            margin: 0 0 1.5rem 0;
        }

        /* Input Controls with Prefix Icons */
        .input-box {
            position: relative;
            margin-bottom: 1.15rem;
        }

        .input-icon-prefix {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #928ca8;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            z-index: 2;
        }

        .input-custom {
            width: 100%;
            height: 48px;
            padding: 0 44px 0 46px;
            background-color: var(--niko-input-bg);
            border: 1.5px solid var(--niko-input-border);
            border-radius: 12px;
            font-size: 0.94rem;
            color: #21193d;
            transition: all 0.22s ease-in-out;
            outline: none;
        }

        .input-custom::placeholder {
            color: #a49eb8;
        }

        .input-custom:focus {
            background-color: #ffffff;
            border-color: var(--niko-purple-primary);
            box-shadow: 0 0 0 4px rgba(99, 82, 232, 0.14);
        }

        /* Password Eye Toggle Suffix */
        .input-icon-suffix {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #928ca8;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4px;
            z-index: 2;
            transition: color 0.15s;
        }

        .input-icon-suffix:hover {
            color: var(--niko-purple-primary);
        }

        /* Custom Remember Checkbox */
        .remember-wrap {
            display: flex;
            align-items: center;
            margin: 0.5rem 0 1.4rem 0;
        }

        .custom-checkbox {
            width: 18px;
            height: 18px;
            border-radius: 5px;
            border: 1.5px solid #cac3e6;
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

        .custom-checkbox:checked {
            background-color: var(--niko-purple-primary);
            border-color: var(--niko-purple-primary);
        }

        .custom-checkbox:checked::before {
            content: '';
            width: 9px;
            height: 5px;
            border-left: 2px solid #ffffff;
            border-bottom: 2px solid #ffffff;
            transform: rotate(-45deg) translate(1px, -1px);
        }

        .remember-label {
            margin-left: 9px;
            font-size: 0.88rem;
            color: #4b446a;
            user-select: none;
            cursor: pointer;
        }

        /* Sign In Button (SIGN IN ->) */
        .btn-signin-purple {
            width: 100%;
            height: 48px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #6b5ce7 0%, #5846d8 100%);
            color: #ffffff;
            font-size: 0.95rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(99, 82, 232, 0.4);
            transition: all 0.22s ease-in-out;
        }

        .btn-signin-purple:hover {
            background: linear-gradient(135deg, #7566ee 0%, #4d3bc8 100%);
            box-shadow: 0 8px 25px rgba(99, 82, 232, 0.5);
            transform: translateY(-1px);
        }

        .btn-signin-purple:active {
            transform: translateY(0);
            box-shadow: 0 3px 10px rgba(99, 82, 232, 0.3);
        }

        /* Error Message Pill */
        .error-alert-wrap {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #e53e3e;
            font-size: 0.84rem;
            margin-top: 0.85rem;
            font-weight: 500;
        }

        /* Footer Copyright */
        .card-footer-copy {
            margin-top: 1.85rem;
            text-align: center;
            font-size: 0.74rem;
            color: #8f8aab;
        }

        /* Responsive Breakpoints */
        @media (max-width: 1199.98px) {
            .login-left-content {
                width: 50%;
                padding: 2.5rem 2.5rem;
            }
            .login-right-content {
                width: 50%;
                padding: 2rem 2rem;
            }
            .hero-title {
                font-size: 2rem;
            }
        }

        @media (max-width: 991.98px) {
            .login-layout {
                flex-direction: column;
            }
            .login-left-content {
                display: none;
            }
            .login-right-content {
                width: 100%;
                min-height: 100vh;
                padding: 2.5rem 1.5rem;
            }
            .floating-card {
                padding: 2.2rem 1.8rem;
            }
            .facet-ribbon-primary, .facet-ribbon-accent, .facet-ribbon-bottom {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="login-layout">
        <!-- 1. Full Background Image Layer Across Entire Viewport -->
        <div class="login-bg-layer">
            <img 
                src="{{ url('assets/images/login.png') }}?v={{ filemtime(public_path('assets/images/login.png')) }}" 
                alt="NIKO Garments Manufacturing Floor" 
                class="login-bg-img" 
            />
        </div>

        <!-- 2. Dark Vignette & Soft Gradient Overlay -->
        <div class="login-bg-overlay"></div>

        <!-- 3. Right Ambient Violet Glow (Translucent so factory photo shows behind card) -->
        <div class="login-right-ambient"></div>

        <!-- 4. Translucent Diagonal Geometric Ribbons / Facets (Matching Image 2) -->
        <div class="facet-ribbon-primary d-none d-lg-block"></div>
        <div class="facet-ribbon-accent d-none d-lg-block"></div>
        <div class="facet-ribbon-bottom d-none d-lg-block"></div>

        <!-- 5. Left Side Content (Brand Badge + Hero Title + Workflow) -->
        <div class="login-left-content">
            <!-- Top-Left Glass Brand Badge -->
            <div class="brand-badge-top">
                <div class="brand-badge-icon">
                    <img src="{{ url('assets/images/niko_crest.jpg') }}" alt="NIKO Logo">
                </div>
                <div class="d-flex flex-column">
                    <span class="brand-badge-name">NIKO</span>
                    <span class="brand-badge-sub">GARMENTS ERP &bull; NACHIAS</span>
                </div>
            </div>

            <!-- Bottom Left Hero Headline & Workflow Sequence -->
            <div class="hero-caption-wrap">
                <h1 class="hero-title">Precision in Every <span class="highlight-stitch">Stitch.</span></h1>
                <p class="hero-desc">Connecting every stage of shirt manufacturing &mdash; from fabric cutting to final dispatch.</p>
                
                <!-- Workflow: CUTTING -> STITCHING -> QUALITY -> DISPATCH -->
                <div class="workflow-row">
                    <!-- CUTTING -->
                    <div class="workflow-step">
                        <span class="workflow-icon-btn">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="6" cy="6" r="3"></circle>
                                <circle cx="6" cy="18" r="3"></circle>
                                <line x1="20" y1="4" x2="8.12" y2="15.88"></line>
                                <line x1="14.47" y1="14.48" x2="20" y2="20"></line>
                                <line x1="8.12" y1="8.12" x2="12" y2="12"></line>
                            </svg>
                        </span>
                        <span class="workflow-label">CUTTING</span>
                    </div>

                    <span class="workflow-arrow">&rarr;</span>

                    <!-- STITCHING -->
                    <div class="workflow-step">
                        <span class="workflow-icon-btn">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.38 3.46L16 2a4 4 0 0 0-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23z"></path>
                            </svg>
                        </span>
                        <span class="workflow-label">STITCHING</span>
                    </div>

                    <span class="workflow-arrow">&rarr;</span>

                    <!-- QUALITY -->
                    <div class="workflow-step">
                        <span class="workflow-icon-btn">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg>
                        </span>
                        <span class="workflow-label">QUALITY</span>
                    </div>

                    <span class="workflow-arrow">&rarr;</span>

                    <!-- DISPATCH -->
                    <div class="workflow-step">
                        <span class="workflow-icon-btn">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="1" y="3" width="15" height="13"></rect>
                                <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                                <circle cx="5.5" cy="18.5" r="2.5"></circle>
                                <circle cx="18.5" cy="18.5" r="2.5"></circle>
                            </svg>
                        </span>
                        <span class="workflow-label">DISPATCH</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. Right Side Content (Floating Rounded White Login Card) -->
        <div class="login-right-content">
            <div class="floating-card">
                <!-- NIKO Branding Header in Card -->
                <div class="card-brand-header">
                    <div class="card-brand-icon">
                        <img src="{{ url('assets/images/niko_crest.jpg') }}" alt="NIKO Logo">
                    </div>
                    <div>
                        <div class="card-brand-name">NIKO</div>
                        <div class="card-brand-sub">GARMENTS ERP &bull; NACHIAS</div>
                    </div>
                </div>

                <!-- Welcome Text -->
                <h2 class="card-title">Welcome Back</h2>
                <p class="card-subtitle">Sign in to access your manufacturing workspace.</p>

                <!-- Session Flash Messages -->
                @include('flash_messages')

                <!-- Authentication Form -->
                <form id="formAuthentication" action="{{ url('login') }}" method="POST" autocomplete="off">
                    @csrf

                    <!-- Email Field with Mail Prefix Icon -->
                    <div class="input-box">
                        <span class="input-icon-prefix">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                            </svg>
                        </span>
                        <input 
                            type="text" 
                            class="input-custom" 
                            id="email" 
                            name="email" 
                            placeholder="Email" 
                            value="{{ old('email', Cookie::get('email') ?? '') }}" 
                            required 
                        />
                    </div>

                    <!-- Password Field with Lock Prefix & Eye Toggle Suffix -->
                    <div class="input-box">
                        <span class="input-icon-prefix">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </span>
                        <input 
                            type="password" 
                            class="input-custom" 
                            id="form-alignment-password" 
                            name="password" 
                            placeholder="Password" 
                            value="{{ old('password', Cookie::get('password') ?? '') }}" 
                            onkeypress="return event.charCode !== 32" 
                            required 
                        />
                        <span class="input-icon-suffix" id="pwdToggleBtn" role="button" aria-label="Toggle password visibility">
                            <svg id="iconEye" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"></path>
                                <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"></path>
                                <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"></path>
                                <line x1="2" y1="2" x2="22" y2="22"></line>
                            </svg>
                        </span>
                    </div>

                    <!-- Remember Me Checkbox -->
                    <div class="remember-wrap">
                        <input 
                            type="checkbox" 
                            class="custom-checkbox" 
                            name="remember" 
                            id="remember-me" 
                            value="1" 
                            {{ Cookie::get('remember') ? 'checked' : '' }} 
                        />
                        <label class="remember-label" for="remember-me">Remember Me</label>
                    </div>

                    <!-- Sign In Purple Button -->
                    <button class="btn-signin-purple" type="submit" id="btnSignIn">
                        SIGN IN &rarr;
                    </button>

                    <!-- Validation Error Messages -->
                    @if ($errors->has('email') || $errors->has('password'))
                        <div class="error-alert-wrap">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            <span>{{ $errors->first('email') ?: $errors->first('password') }}</span>
                        </div>
                    @endif
                </form>

                <!-- Company Copyright -->
                <div class="card-footer-copy">
                    Nachias Fashion Private Limited &copy; {{ date('Y') }} &bull; All Rights Reserved
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ url('assets/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ url('assets/js/bootstrap.js') }}"></script>

    <!-- Password Eye Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var toggleBtn = document.getElementById('pwdToggleBtn');
            var pwdInput = document.getElementById('form-alignment-password');
            var iconEye = document.getElementById('iconEye');

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