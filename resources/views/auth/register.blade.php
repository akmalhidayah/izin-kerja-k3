<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Register - K3 PT. Semen Tonasa</title>
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

        .auth-shell {
            height: 100vh;
            height: 100dvh;
            display: grid;
            grid-template-columns: minmax(0, 0.68fr) minmax(380px, 0.92fr);
            align-items: stretch;
        }

        .auth-visual {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 26px;
            overflow: hidden;
        }

        .auth-visual::before {
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

        .auth-visual h1 {
            margin: 18px 0 0;
            font-size: clamp(24px, 3vw, 38px);
            line-height: 1.02;
            letter-spacing: 0;
        }

        .auth-visual h1 span {
            display: block;
            margin-top: 8px;
            font-size: 0.72em;
            line-height: 1.08;
        }

        .auth-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(248, 250, 252, 0.92);
            backdrop-filter: blur(18px);
        }

        .auth-card {
            width: 100%;
            max-width: 360px;
            padding: 22px;
            border: 1px solid rgba(148, 163, 184, 0.3);
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 24px 70px rgba(15, 23, 42, 0.18);
        }

        .auth-logo-group {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
        }

        .logo-box {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 44px;
            padding: 7px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #fff;
        }

        .auth-logo {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .auth-card h2 {
            margin: 0 0 14px;
            font-size: 24px;
            line-height: 1.2;
            color: #991b1b;
        }

        .alert {
            margin-bottom: 12px;
            padding: 10px 12px;
            border-radius: 12px;
            font-size: 13px;
        }

        .alert-danger {
            color: #991b1b;
            background: #fee2e2;
            border: 1px solid #fecaca;
        }

        .form-stack {
            display: grid;
            gap: 10px;
        }

        .form-label {
            display: block;
            margin-bottom: 5px;
            color: #334155;
            font-size: 13px;
            font-weight: 700;
        }

        .form-control {
            width: 100%;
            min-height: 40px;
            padding: 8px 11px;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            background: #f8fafc;
            color: #172033;
            font-size: 14px;
            outline: none;
            transition: border-color 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
        }

        .form-control:focus {
            border-color: #dc2626;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.12);
        }

        .field-error {
            margin-top: 5px;
            color: #b91c1c;
            font-size: 12px;
            line-height: 1.35;
        }

        .form-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-top: 2px;
            font-size: 13px;
        }

        .auth-link {
            color: #b91c1c;
            font-weight: 700;
            text-decoration: none;
        }

        .auth-link:hover {
            text-decoration: underline;
        }

        .auth-button {
            min-height: 42px;
            min-width: 120px;
            border: 0;
            border-radius: 12px;
            background: linear-gradient(135deg, #dc2626, #991b1b);
            color: #fff;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 12px 24px rgba(185, 28, 28, 0.24);
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }

        .auth-button:hover {
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

            .auth-shell {
                width: 100%;
                min-height: auto;
                display: flex;
                justify-content: center;
            }

            .auth-visual {
                display: none;
            }

            .auth-panel {
                width: 100%;
                padding: 0;
                background: transparent;
                backdrop-filter: none;
            }

            .auth-card {
                max-width: 420px;
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

            .auth-panel {
                min-height: calc(100dvh - 28px);
                align-items: center;
            }

            .auth-card {
                padding: 22px;
                border-radius: 18px;
            }

            .form-control {
                font-size: 16px;
            }

            .form-row {
                align-items: stretch;
                flex-direction: column-reverse;
            }

            .auth-button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <main class="auth-shell">
        <section class="auth-visual">
            <div class="visual-content">
                <div class="eyebrow">Sistem K3</div>
                <h1>Unit Keselamatan dan Kesehatan Kerja<span>PT. Semen Tonasa</span></h1>
            </div>
        </section>

        <section class="auth-panel">
            <div class="auth-card">
                <div class="auth-logo-group">
                    <div class="logo-box">
                        <img src="{{ asset('images/logo-sig.png') }}" alt="SIG" class="auth-logo">
                    </div>
                    <div class="logo-box">
                        <img src="{{ asset('images/logo-st2.png') }}" alt="Semen Tonasa" class="auth-logo">
                    </div>
                    <div class="logo-box">
                        <img src="{{ asset('images/logo-k3.png') }}" alt="K3" class="auth-logo">
                    </div>
                </div>

                <h2>Daftar</h2>

                @if ($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="form-stack">
                    @csrf

                    <div>
                        <label class="form-label" for="name">Nama</label>
                        <input class="form-control" id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
                        @error('name')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label" for="email">Email</label>
                        <input class="form-control" id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
                        @error('email')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label" for="password">Password</label>
                        <input class="form-control" id="password" type="password" name="password" required autocomplete="new-password">
                        @error('password')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                        <input class="form-control" id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
                        @error('password_confirmation')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-row">
                        <a class="auth-link" href="{{ route('login') }}">Sudah terdaftar?</a>
                        <button class="auth-button" type="submit">Daftar</button>
                    </div>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
