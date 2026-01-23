<!DOCTYPE html>
<html lang="en" class="light-style" dir="ltr" data-theme="theme-default" data-assets-path="{{ url('assets') }}/" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Page Not Found - Error 404</title>
    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ url('assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ url('assets/fonts/iconify-icons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ url('assets/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ url('assets/css/demo.css') }}" class="template-customizer-theme-css" />

    <!-- Page CSS -->
    <style>
        body {
            background-color: #f5f5f9;
            background-image: radial-gradient(at 0% 0%, hsla(253,16%,7%,0.33) 0, transparent 50%), 
                              radial-gradient(at 50% 0%, hsla(225,39%,30%,0.33) 0, transparent 50%), 
                              radial-gradient(at 100% 0%, hsla(339,49%,30%,0.33) 0, transparent 50%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            overflow: hidden;
            position: relative;
        }

        .misc-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .error-header {
            font-size: 8rem;
            font-weight: 800;
            line-height: 1;
            background: -webkit-linear-gradient(45deg, #696cff, #888bff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0;
            position: relative;
            animation: float 6s ease-in-out infinite;
        }

        .error-subheader {
            font-size: 1.75rem;
            font-weight: 600;
            color: #566a7f;
            margin-top: 1rem;
            margin-bottom: 1rem;
        }

        .error-text {
            color: #697a8d;
            margin-bottom: 2rem;
            max-width: 500px;
            font-size: 1.1rem;
        }

        .btn-modern {
            padding: 0.75rem 2rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 14px 0 rgba(105, 108, 255, 0.39);
            transition: all 0.2s ease-in-out;
        }

        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px 0 rgba(105, 108, 255, 0.23);
        }

        .bg-shape {
            position: absolute;
            z-index: 0;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.6;
        }

        .shape-1 {
            width: 300px;
            height: 300px;
            background: #696cff;
            top: -50px;
            left: -50px;
            animation: moveShape1 10s infinite alternate;
        }

        .shape-2 {
            width: 400px;
            height: 400px;
            background: #8592a3;
            bottom: -100px;
            right: -100px;
            opacity: 0.3;
            animation: moveShape2 15s infinite alternate;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }

        @keyframes moveShape1 {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }

        @keyframes moveShape2 {
            0% { transform: translate(0, 0); }
            100% { transform: translate(-50px, -50px); }
        }

        /* Glassmorphism Card */
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 1.5rem;
            padding: 3rem;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1);
            position: relative;
            z-index: 10;
        }

        .icon-container {
            font-size: 5rem;
            color: #696cff;
            margin-bottom: 1rem;
            animation: pulse 3s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.8; }
            100% { transform: scale(1); opacity: 1; }
        }

    </style>
</head>

<body>
    <!-- Background Shapes -->
    <div class="bg-shape shape-1"></div>
    <div class="bg-shape shape-2"></div>

    <!-- Error Content -->
    <div class="container-xxl container-p-y text-center">
        <div class="misc-wrapper glass-card mx-auto" style="max-width: 600px;">
            <div class="icon-container">
                <i class="ri-error-warning-line"></i>
            </div>
            <h2 class="error-header">404</h2>
            <h4 class="error-subheader">Page Not Found ⚠️</h4>
            <p class="error-text">
                Oops! We couldn't find the page you are looking for. <br>
                It might have been removed, renamed, or temporarily unavailable.
            </p>
            <a href="{{ url('/') }}" class="btn btn-primary btn-modern">Back to Home</a>
        </div>
    </div>
</body>
</html>
