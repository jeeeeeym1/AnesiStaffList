@extends('layouts.main')

@section('title', 'Sign In — Anesi Staff System')

@section('styles')
<style>
    :root {
        --forest: #1B3A2D; --forest-soft: #2D6147;
        --cream: #F5F0E8; --gold: #C8922A;
        --text: #1A1209; --muted: #8A7A65;
        --border: #DDD3C0; --input-bg: #F8F4EE;
        --white: #FFFFFF; --error: #B83030;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        min-height: 100vh; font-family: 'DM Sans', sans-serif;
        background: var(--cream); display: flex; flex-direction: column;
        align-items: center; justify-content: center; padding: 24px 16px;
        -webkit-font-smoothing: antialiased;
    }
    body::before {
        content: ''; position: fixed; top: 0; left: 0; right: 0; height: 3px;
        background: linear-gradient(90deg, var(--forest), var(--gold), var(--forest));
    }
    .card {
        width: 100%; max-width: 400px; background: var(--white);
        border-radius: 16px; padding: 40px 36px 32px;
        border: 1px solid var(--border);
        box-shadow: 0 4px 24px rgba(27,58,45,0.10);
        animation: rise 0.5s cubic-bezier(0.22,1,0.36,1) both;
    }
    @keyframes rise { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
    .brand { display:flex; flex-direction:column; align-items:center; margin-bottom:28px; }
    .brand-logo {
        width:44px; height:44px; background:var(--forest); border-radius:12px;
        display:flex; align-items:center; justify-content:center;
        font-family:'Lora',serif; font-size:20px; font-weight:700; color:var(--white);
        margin-bottom:14px; box-shadow:0 3px 12px rgba(27,58,45,0.25);
    }
    .brand h1 { font-family:'Lora',serif; font-size:22px; font-weight:700; color:var(--text); margin-bottom:4px; }
    .brand p { font-size:13px; color:var(--muted); }
    hr { border:none; border-top:1px solid var(--border); margin:0 0 24px; }
    .field { margin-bottom:16px; }
    label { display:block; font-size:11px; font-weight:600; letter-spacing:1.2px; text-transform:uppercase; color:var(--muted); margin-bottom:6px; }
    .input-wrap { position:relative; }
    input[type="email"], input[type="password"] {
        width:100%; padding:11px 40px 11px 14px; background:var(--input-bg);
        border:1.5px solid var(--border); border-radius:8px;
        font-family:'DM Sans',sans-serif; font-size:14px; color:var(--text);
        outline:none; transition:border-color .18s, box-shadow .18s;
    }
    input::placeholder { color:#b0a898; }
    input:focus { border-color:var(--forest-soft); background:var(--white); box-shadow:0 0 0 3px rgba(27,58,45,0.08); }
    input.is-invalid { border-color:var(--error); }
    .icon { position:absolute; right:12px; top:50%; transform:translateY(-50%); color:#b0a898; display:flex; cursor:pointer; }
    .icon svg { width:16px; height:16px; }
    .error-msg { font-size:12px; color:var(--error); margin-top:4px; font-weight:500; }
    .row { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; }
    .check-label { display:flex; align-items:center; gap:7px; font-size:13px; color:var(--muted); cursor:pointer; }
    .check-label input[type="checkbox"] {
        appearance:none; width:15px; height:15px; border:1.5px solid var(--border);
        border-radius:4px; background:var(--input-bg); cursor:pointer; position:relative;
        transition:background .15s, border-color .15s;
    }
    .check-label input[type="checkbox"]:checked { background:var(--forest); border-color:var(--forest); }
    .check-label input[type="checkbox"]:checked::after {
        content:''; position:absolute; top:1px; left:4px; width:4px; height:7px;
        border:2px solid var(--white); border-top:none; border-left:none; transform:rotate(45deg);
    }
    .forgot { font-size:13px; font-weight:600; color:var(--gold); text-decoration:none; }
    .forgot:hover { color:var(--forest); }
    .btn {
        width:100%; padding:13px; background:var(--forest); color:var(--white);
        border:none; border-radius:8px; font-family:'DM Sans',sans-serif;
        font-size:14px; font-weight:600; cursor:pointer;
        transition:background .18s, transform .12s;
        box-shadow:0 3px 12px rgba(27,58,45,0.20);
    }
    .btn:hover { background:var(--forest-soft); transform:translateY(-1px); }
    .btn:active { transform:translateY(0); }
    .foot { text-align:center; font-size:13px; color:var(--muted); margin-top:18px; }
    .foot a { color:var(--text); font-weight:600; text-decoration:underline; text-underline-offset:2px; }
    .foot a:hover { color:var(--forest); }
    .alert {
        background:rgba(184,48,48,0.07); border:1px solid rgba(184,48,48,0.18);
        color:var(--error); border-radius:8px; padding:10px 14px;
        font-size:13px; font-weight:500; margin-bottom:18px;
    }
    .alert-success {
        background:rgba(27,58,45,0.07); border:1px solid rgba(27,58,45,0.18);
        color:var(--forest); border-radius:8px; padding:10px 14px;
        font-size:13px; font-weight:500; margin-bottom:18px;
    }
    .version { font-size:11px; color:var(--muted); margin-top:24px; text-align:center; }
</style>
@endsection

@section('content')
<div class="card">
    <div class="brand">
        <div class="brand-logo">À</div>
        <h1>Welcome back</h1>
        <p>Anesi Iced Coffee · Staff Portal</p>
    </div>
    <hr>

    @if(session('toast_success'))
        <div class="alert-success">{{ session('toast_success') }}</div>
    @endif

    @if($errors->has('email') && $errors->first('email') === 'auth')
        <div class="alert">These credentials do not match our records.</div>
    @endif

    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf
        <div class="field">
            <label for="email">Email Address</label>
            <div class="input-wrap">
                <input id="email" type="email" name="email"
                    class="{{ $errors->has('email') && $errors->first('email') !== 'auth' ? 'is-invalid' : '' }}"
                    value="{{ old('email') }}" placeholder="admin@anesi.com"
                    required autocomplete="email" autofocus>
                <span class="icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="4" width="20" height="16" rx="3"/><path d="M2 7l10 7 10-7"/>
                    </svg>
                </span>
            </div>
            @if($errors->has('email') && $errors->first('email') !== 'auth')
                <div class="error-msg">{{ $errors->first('email') }}</div>
            @endif
        </div>

        <div class="field">
            <label for="password">Password</label>
            <div class="input-wrap">
                <input id="password" type="password" name="password"
                    class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                    placeholder="Enter your password" required autocomplete="current-password">
                <span class="icon" id="togglePw" role="button" aria-label="Toggle password" tabindex="0">
                    <svg id="eyeIco" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                    </svg>
                </span>
            </div>
            @error('password')<div class="error-msg">{{ $message }}</div>@enderror
        </div>

        <div class="row">
            <label class="check-label">
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                Remember me
            </label>
        </div>

        <button type="submit" class="btn">Sign In</button>
    </form>

    @if(Route::has('register'))
        <p class="foot">Don't have an account? <a href="{{ route('register') }}">Register here →</a></p>
    @endif
</div>

<p class="version">Anesi Staff System v1.0 · © {{ date('Y') }} Anesi Iced Coffee</p>
@endsection

@section('scripts')
<script>
    const pw = document.getElementById('password');
    const btn = document.getElementById('togglePw');
    const ico = document.getElementById('eyeIco');
    const open  = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>`;
    const slash = `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>`;
    function toggle() { const s = pw.type==='password'; pw.type = s?'text':'password'; ico.innerHTML = s?slash:open; }
    btn.addEventListener('click', toggle);
    btn.addEventListener('keydown', e => (e.key==='Enter'||e.key===' ') && toggle());
</script>
@endsection
