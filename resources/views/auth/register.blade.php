@extends('layouts.main')

@section('title', 'Register — Anesi Staff System')

@section('styles')
<style>
    :root {
        --forest: #1B3A2D; --forest-soft: #2D6147;
        --cream: #F5F0E8; --gold: #C8922A;
        --text: #1A1209; --muted: #8A7A65;
        --border: #DDD3C0; --input-bg: #F8F4EE;
        --white: #FFFFFF; --error: #B83030;
    }
    *, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
    body {
        min-height:100vh; font-family:'DM Sans',sans-serif; background:var(--cream);
        display:flex; flex-direction:column; align-items:center; justify-content:center;
        padding:24px 16px; -webkit-font-smoothing:antialiased;
    }
    body::before {
        content:''; position:fixed; top:0; left:0; right:0; height:3px;
        background:linear-gradient(90deg, var(--forest), var(--gold), var(--forest));
    }
    .card {
        width:100%; max-width:420px; background:var(--white); border-radius:16px;
        padding:40px 36px 32px; border:1px solid var(--border);
        box-shadow:0 4px 24px rgba(27,58,45,0.10);
        animation:rise 0.5s cubic-bezier(0.22,1,0.36,1) both;
    }
    @keyframes rise { from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);} }
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
    input[type="text"], input[type="email"], input[type="password"] {
        width:100%; padding:11px 14px; background:var(--input-bg);
        border:1.5px solid var(--border); border-radius:8px;
        font-family:'DM Sans',sans-serif; font-size:14px; color:var(--text);
        outline:none; transition:border-color .18s, box-shadow .18s;
    }
    input::placeholder { color:#b0a898; }
    input:focus { border-color:var(--forest-soft); background:var(--white); box-shadow:0 0 0 3px rgba(27,58,45,0.08); }
    input.is-invalid { border-color:var(--error); }
    .error-msg { font-size:12px; color:var(--error); margin-top:4px; font-weight:500; }
    .btn {
        width:100%; padding:13px; background:var(--forest); color:var(--white);
        border:none; border-radius:8px; font-family:'DM Sans',sans-serif;
        font-size:14px; font-weight:600; cursor:pointer;
        transition:background .18s, transform .12s;
        box-shadow:0 3px 12px rgba(27,58,45,0.20);
    }
    .btn:hover { background:var(--forest-soft); transform:translateY(-1px); }
    .foot { text-align:center; font-size:13px; color:var(--muted); margin-top:18px; }
    .foot a { color:var(--text); font-weight:600; text-decoration:underline; text-underline-offset:2px; }
    .foot a:hover { color:var(--forest); }
    .version { font-size:11px; color:var(--muted); margin-top:24px; text-align:center; }
    .pw-wrap { position:relative; }
    .pw-toggle { position:absolute; right:12px; top:50%; transform:translateY(-50%); color:#b0a898; cursor:pointer; display:flex; }
    .pw-toggle svg { width:16px; height:16px; }
    .select-wrap { position:relative; }
    .select-input {
        width:100%; padding:11px 36px 11px 14px; background:var(--input-bg);
        border:1.5px solid var(--border); border-radius:8px;
        font-family:'DM Sans',sans-serif; font-size:14px; color:var(--text);
        outline:none; appearance:none; cursor:pointer;
        transition:border-color .18s, box-shadow .18s;
    }
    .select-input:focus { border-color:var(--forest-soft); background:var(--white); box-shadow:0 0 0 3px rgba(27,58,45,0.08); }
    .select-input.is-invalid { border-color:var(--error); }
    .select-arrow { position:absolute; right:12px; top:50%; transform:translateY(-50%); color:#b0a898; pointer-events:none; display:flex; }
</style>
@endsection

@section('content')
<div class="card">
    <div class="brand">
        <div class="brand-logo">À</div>
        <h1>Create Account</h1>
        <p>Anesi Iced Coffee · Staff Portal</p>
    </div>
    <hr>

    <form method="POST" action="{{ route('register') }}" novalidate>
        @csrf

        <div class="field">
            <label for="name">Full Name</label>
            <input id="name" type="text" name="name"
                class="{{ $errors->has('name') ? 'is-invalid' : '' }}"
                value="{{ old('name') }}" placeholder="Juan dela Cruz"
                required autocomplete="name" autofocus>
            @error('name')<div class="error-msg">{{ $message }}</div>@enderror
        </div>

        <div class="field">
            <label for="email">Email Address</label>
            <input id="email" type="email" name="email"
                class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                value="{{ old('email') }}" placeholder="juan@anesi.com"
                required autocomplete="email">
            @error('email')<div class="error-msg">{{ $message }}</div>@enderror
        </div>

        <div class="field">
            <label for="password">Password</label>
            <div class="pw-wrap">
                <input id="password" type="password" name="password"
                    class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                    placeholder="Min. 8 characters" required autocomplete="new-password">
                <span class="pw-toggle" id="togglePw1">
                    <svg id="eye1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                    </svg>
                </span>
            </div>
            @error('password')<div class="error-msg">{{ $message }}</div>@enderror
        </div>

        <div class="field">
            <label for="password_confirmation">Confirm Password</label>
            <div class="pw-wrap">
                <input id="password_confirmation" type="password" name="password_confirmation"
                    placeholder="Repeat password" required autocomplete="new-password">
                <span class="pw-toggle" id="togglePw2">
                    <svg id="eye2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                    </svg>
                </span>
            </div>
        </div>

        <div class="field">
            <label for="role">Role</label>
            <div class="select-wrap">
                <select id="role" name="role" class="select-input {{ $errors->has('role') ? 'is-invalid' : '' }}" required>
                    <option value="" disabled {{ old('role') ? '' : 'selected' }}>— Select a role —</option>
                    <option value="admin"   {{ old('role') === 'admin'   ? 'selected' : '' }}>Admin</option>
                    <option value="staff"   {{ old('role') === 'staff'   ? 'selected' : '' }}>Staff</option>
                </select>
                <span class="select-arrow">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:14px;height:14px;"><polyline points="6 9 12 15 18 9"/></svg>
                </span>
            </div>
            @error('role')<div class="error-msg">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn">Create Account</button>
    </form>

    <p class="foot">Already have an account? <a href="{{ route('login') }}">Sign in →</a></p>
</div>

<p class="version">Anesi Staff System v1.0 · © {{ date('Y') }} Anesi Iced Coffee</p>
@endsection

@section('scripts')
<script>
    function makeToggle(btnId, inputId, eyeId) {
        const btn = document.getElementById(btnId);
        const inp = document.getElementById(inputId);
        const ico = document.getElementById(eyeId);
        const open  = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>`;
        const slash = `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>`;
        btn.addEventListener('click', () => { const s = inp.type==='password'; inp.type=s?'text':'password'; ico.innerHTML=s?slash:open; });
    }
    makeToggle('togglePw1','password','eye1');
    makeToggle('togglePw2','password_confirmation','eye2');
</script>
@endsection
