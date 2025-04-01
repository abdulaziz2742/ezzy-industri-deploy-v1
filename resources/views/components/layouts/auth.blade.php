<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>EzzyIndustri - Login</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0a2463 0%, #1e88e5 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }

        .login-container {
            display: flex;
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            width: 1000px;
            max-width: 100%;
        }

        .login-image {
            flex: 1;
            background: linear-gradient(45deg, #1a237e, #0d47a1);
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .login-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><rect x="10" y="30" width="15" height="25" fill="%23ffffff33"/><rect x="35" y="20" width="15" height="35" fill="%23ffffff33"/></svg>');
            background-size: cover;
            opacity: 0.1;
        }

        .login-form {
            flex: 1;
            padding: 40px;
            background: #fff;
        }

        .form-title {
            font-size: 32px;
            font-weight: 700;
            color: #1a237e;
            margin-bottom: 30px;
        }

        .form-control {
            background: #f5f5f5;
            border: none;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .form-control:focus {
            background: #fff;
            box-shadow: 0 0 0 2px #1e88e5;
        }

        .btn-login {
            background: #1e88e5;
            color: #fff;
            padding: 15px;
            border-radius: 10px;
            border: none;
            width: 100%;
            font-weight: 600;
            margin-top: 20px;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: #1565c0;
            transform: translateY(-2px);
        }

        .form-check {
            margin: 15px 0;
        }

        .form-text {
            color: #666;
        }

        .form-text a {
            color: #1e88e5;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .login-image {
                display: none;
            }
            
            .login-container {
                width: 100%;
                max-width: 400px;
            }
        }

        .night-scene {
            position: relative;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .moon {
            width: 60px;
            height: 60px;
            background: #fff;
            border-radius: 50%;
            box-shadow: 0 0 20px rgba(255,255,255,0.8);
            position: absolute;
            top: 20%;
            right: 20%;
        }

        .factory {
            position: relative;
            z-index: 1;
            color: #fff;
            text-align: center;
        }

        .factory h2 {
            font-size: 24px;
            margin-bottom: 15px;
            color: #fff;
        }

        .factory p {
            color: rgba(255,255,255,0.8);
            font-size: 16px;
        }

        .doc-button {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: white;
            text-decoration: none;
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
        }

        .doc-button:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            transform: translateY(-2px);
        }
    </style>
</head>

<body>
    <a href="{{ route('documentation') }}" class="doc-button">
        Documentation
    </a>
    <div class="login-container">
        <div class="login-image">
            <div class="night-scene">
                <div class="moon"></div>
                <div class="factory">
                    <h2>EzzyIndustri</h2>
                    <p>Sistem Manajemen Produksi Modern</p>
                </div>
            </div>
        </div>
        <div class="login-form">
            {{ $slot }}
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>