<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Login - K3 PT. Semen Tonasa</title>
    <link rel="icon" href="{{ asset('images/logo-k3.png') }}">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            height: 100vh;
            height: 100dvh;
            overflow: hidden;
            font-family: Arial, Helvetica, sans-serif;
            color: #172033;
            background:
                linear-gradient(135deg, rgba(153, 27, 27, 0.9), rgba(15, 23, 42, 0.72)),
                url("{{ asset('images/bg-login.jpg') }}") center / cover no-repeat fixed;
        }

        .login-shell {
            height: 100vh;
            height: 100dvh;
            display: grid;
            grid-template-columns: minmax(0, 0.68fr) minmax(380px, 0.92fr);
            align-items: stretch;
        }

        .login-visual {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 26px;
            overflow: hidden;
        }

        .login-visual::before {
            content: "";
            position: absolute;
            inset: 18px;
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 28px;
        }

        .visual-content {
            position: relative;
            max-width: 720px;
            color: white;
            text-align: center;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 7px 11px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.22);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .eyebrow::before {
            content: "";
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: #fbbf24;
            box-shadow: 0 0 0 6px rgba(251, 191, 36, 0.18);
        }

        .login-visual h1 {
            margin: 18px 0 0;
            font-size: clamp(24px, 3vw, 38px);
            line-height: 1.02;
            letter-spacing: 0;
        }

        .login-visual h1 span {
            display: block;
            margin-top: 8px;
            font-size: 0.72em;
            line-height: 1.08;
        }

        .login-visual p {
            margin: 0;
            max-width: 580px;
            color: rgba(255, 255, 255, 0.84);
            font-size: 16px;
            line-height: 1.7;
        }

        .login-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(248, 250, 252, 0.92);
            backdrop-filter: blur(18px);
        }

        .login-card {
            width: 100%;
            max-width: 520px;
            padding: 34px;
            border: 1px solid rgba(148, 163, 184, 0.3);
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 24px 70px rgba(15, 23, 42, 0.18);
        }

        .login-logo-group {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 26px;
        }

        .logo-box {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 84px;
            height: 64px;
            padding: 10px;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            background: #fff;
        }

        .login-logo {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .login-card h2 {
            margin: 0;
            font-size: 34px;
            line-height: 1.2;
            color: #991b1b;
        }

        .alert {
            margin-bottom: 18px;
            padding: 12px 14px;
            border-radius: 12px;
            font-size: 13px;
        }

        .alert-danger {
            color: #991b1b;
            background: #fee2e2;
            border: 1px solid #fecaca;
        }

        .alert-success {
            color: #166534;
            background: #dcfce7;
            border: 1px solid #bbf7d0;
        }

        .form-stack {
            display: grid;
            gap: 16px;
        }

        .form-label {
            display: block;
            margin-bottom: 7px;
            color: #334155;
            font-size: 13px;
            font-weight: 700;
        }

        .form-control {
            width: 100%;
            min-height: 54px;
            padding: 13px 16px;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            background: #f8fafc;
            color: #172033;
            font-size: 16px;
            outline: none;
            transition: border-color 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
        }

        .form-control:focus {
            border-color: #dc2626;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.12);
        }

        .form-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            font-size: 15px;
        }

        .remember {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #475569;
            user-select: none;
        }

        .remember input {
            width: 16px;
            height: 16px;
            accent-color: #dc2626;
        }

        .forgot-link {
            color: #b91c1c;
            font-weight: 700;
            text-decoration: none;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        .login-button {
            width: 100%;
            min-height: 56px;
            border: 0;
            border-radius: 12px;
            background: linear-gradient(135deg, #dc2626, #991b1b);
            color: #fff;
            font-size: 17px;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 12px 24px rgba(185, 28, 28, 0.24);
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }

        .login-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 30px rgba(185, 28, 28, 0.3);
        }

        @media (max-width: 900px) {
            body {
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 18px;
                overflow-x: hidden;
                overflow-y: auto;
                background-attachment: scroll;
            }

            .login-shell {
                width: 100%;
                min-height: auto;
                display: flex;
                justify-content: center;
            }

            .login-visual {
                display: none;
            }

            .login-panel {
                width: 100%;
                padding: 0;
                background: transparent;
                backdrop-filter: none;
            }

            .login-card {
                max-width: 520px;
                margin: 0 auto;
            }
        }

        @media (max-width: 520px) {
            body {
                padding: 14px;
                background:
                    linear-gradient(135deg, rgba(153, 27, 27, 0.78), rgba(15, 23, 42, 0.66)),
                    url("{{ asset('images/bg-login.jpg') }}") center / cover no-repeat;
            }

            .login-panel {
                min-height: calc(100dvh - 28px);
                align-items: center;
            }

            .login-card {
                padding: 22px;
                border-radius: 18px;
            }

            .login-logo-group {
                gap: 8px;
            }

            .logo-box {
                width: 58px;
                height: 46px;
                padding: 7px;
            }

            .login-card h2 {
                font-size: 24px;
            }

            .form-control {
                font-size: 16px;
                min-height: 44px;
                padding: 10px 12px;
            }

            .login-button {
                min-height: 46px;
                font-size: 15px;
            }

            .form-row {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <main class="login-shell">
        <section class="login-visual">
            <div class="visual-content">
                <div class="eyebrow">Sistem K3</div>
                <h1>Unit Keselamatan dan Kesehatan Kerja<span>PT. Semen Tonasa</span></h1>
            </div>
        </section>

        <section class="login-panel">
            <div class="login-card">
                <div class="login-logo-group">
                    <div class="logo-box">
                        <img src="{{ asset('images/logo-sig.png') }}" alt="SIG" class="login-logo">
                    </div>
                    <div class="logo-box">
                        <img src="{{ asset('images/logo-st2.png') }}" alt="Semen Tonasa" class="login-logo">
                    </div>
                    <div class="logo-box">
                        <img src="{{ asset('images/logo-k3.png') }}" alt="K3" class="login-logo">
                    </div>
                </div>

                <h2>Masuk</h2>

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="form-stack">
                    @csrf

                    <div>
                        <label class="form-label" for="email">Email</label>
                        <input class="form-control" id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                    </div>

                    <div>
                        <label class="form-label" for="password">Password</label>
                        <input class="form-control" id="password" type="password" name="password" required autocomplete="current-password">
                    </div>

                    <div class="form-row">
                        <label class="remember" for="remember_me">
                            <input type="checkbox" name="remember" id="remember_me">
                            <span>Ingat saya</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="forgot-link" href="{{ route('password.request') }}">Lupa password?</a>
                        @endif
                    </div>

                    <button class="login-button" type="submit">Login</button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
