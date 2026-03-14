<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password — PMR Wira 242</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at top center, #4a2f24 0%, #3b241f 45%, #22140f 100%);
            padding: 24px;
        }

        .auth-card {
            background: #2e1c17;
            border: 1px solid rgba(232,211,178,.15);
            border-top: 4px solid #c0392b;
            border-radius: 16px;
            width: 100%;
            max-width: 420px;
            padding: 40px 36px 36px;
            box-shadow: 0 24px 60px rgba(0,0,0,.55);
        }

        .auth-logo-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            margin-bottom: 28px;
        }

        .auth-logo-row img {
            height: 52px;
            width: 52px;
            object-fit: contain;
            border-radius: 50%;
        }

        .auth-brand { display: flex; flex-direction: column; line-height: 1.2; }
        .auth-brand-main { font-size: 17px; font-weight: 700; color: #f7f2ec; letter-spacing: .04em; }
        .auth-brand-sub  { font-size: 11.5px; font-weight: 500; color: rgba(232,211,178,.55); letter-spacing: .06em; text-transform: uppercase; }

        .auth-divider { height: 1px; background: rgba(232,211,178,.12); margin-bottom: 28px; }

        .auth-title    { text-align: center; font-size: 22px; font-weight: 700; color: #f7f2ec; margin-bottom: 6px; }
        .auth-subtitle { text-align: center; font-size: 13px; color: rgba(232,211,178,.5); margin-bottom: 28px; line-height: 1.6; }

        .auth-alert {
            background: rgba(192,57,43,.18);
            border: 1px solid rgba(192,57,43,.4);
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            color: #f4a59a;
            margin-bottom: 18px;
        }

        .auth-alert-success {
            background: rgba(39,174,96,.15);
            border-color: rgba(39,174,96,.35);
            color: #7dddaa;
        }

        .form-group { margin-bottom: 22px; }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: rgba(232,211,178,.75);
            margin-bottom: 7px;
            letter-spacing: .02em;
        }

        .form-input {
            width: 100%;
            background: rgba(255,255,255,.06);
            border: 1.5px solid rgba(232,211,178,.25);
            border-radius: 9px;
            padding: 11px 14px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            color: #f7f2ec;
            outline: none;
            transition: border-color .2s, background .2s;
        }

        .form-input::placeholder { color: rgba(232,211,178,.3); }
        .form-input:focus { border-color: rgba(192,57,43,.7); background: rgba(255,255,255,.09); }
        .form-input.is-error { border-color: #e74c3c; }
        .form-error { font-size: 12px; color: #f4a59a; margin-top: 5px; }

        .btn-submit {
            width: 100%;
            background: #c0392b;
            color: #fff;
            border: none;
            border-radius: 9px;
            padding: 13px;
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            cursor: pointer;
            transition: background .2s, transform .15s, box-shadow .2s;
        }

        .btn-submit:hover { background: #a93226; transform: translateY(-1px); box-shadow: 0 6px 20px rgba(192,57,43,.4); }
        .btn-submit:active { transform: translateY(0); }

        .auth-footer-link {
            text-align: center;
            margin-top: 22px;
            font-size: 13px;
            color: rgba(232,211,178,.45);
        }

        .auth-footer-link a { color: rgba(232,211,178,.75); text-decoration: none; font-weight: 600; }
        .auth-footer-link a:hover { color: #f7f2ec; text-decoration: underline; }

        .auth-cross-deco { position: fixed; opacity: .04; pointer-events: none; }
        .auth-cross-deco.top-left  { top: -60px; left: -60px; width: 260px; height: 260px; }
        .auth-cross-deco.bot-right { bottom: -60px; right: -60px; width: 260px; height: 260px; }
    </style>
</head>
<body>
    <svg class="auth-cross-deco top-left" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        <rect x="38" y="5"  width="24" height="90" rx="8" fill="#c0392b"/>
        <rect x="5"  y="38" width="90" height="24" rx="8" fill="#c0392b"/>
    </svg>
    <svg class="auth-cross-deco bot-right" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
        <rect x="38" y="5"  width="24" height="90" rx="8" fill="#c0392b"/>
        <rect x="5"  y="38" width="90" height="24" rx="8" fill="#c0392b"/>
    </svg>

    <div class="auth-card">
        <div class="auth-logo-row">
            <img src="{{ asset('images/logo/logo-pmr.jpg') }}" alt="Logo PMR">
            <div class="auth-brand">
                <span class="auth-brand-main">PMR Wira 242</span>
                <span class="auth-brand-sub">MAN 3 Makassar</span>
            </div>
        </div>

        <div class="auth-divider"></div>

        <h1 class="auth-title">Lupa Password</h1>
        <p class="auth-subtitle">Masukkan email akun Anda. Kami akan mengirimkan tautan untuk mereset password.</p>

        @if (session('status'))
            <div class="auth-alert auth-alert-success">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="auth-alert">
                @foreach ($errors->all() as $err) {{ $err }}<br> @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input id="email" class="form-input {{ $errors->has('email') ? 'is-error' : '' }}"
                       type="email" name="email" value="{{ old('email') }}"
                       required autofocus
                       placeholder="contoh@email.com">
                @error('email')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn-submit">Kirim Link Reset</button>
        </form>

        <p class="auth-footer-link">
            Ingat password? <a href="{{ route('login') }}">Kembali Login</a>
        </p>
    </div>
</body>
</html>
