<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Unauthorized · Anesi Staff System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@600;700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root { --forest:#1B3A2D; --gold:#C8922A; --cream:#F5F0E8; --border:#DDD3C0; --muted:#8A7A65; }
        *, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
        body {
            min-height:100vh; font-family:'DM Sans',sans-serif; background:var(--cream);
            display:flex; align-items:center; justify-content:center; padding:24px;
        }
        body::before {
            content:''; position:fixed; top:0; left:0; right:0; height:3px;
            background:linear-gradient(90deg,var(--forest),var(--gold),var(--forest));
        }
        .box {
            background:#fff; border-radius:16px; padding:48px 40px; text-align:center;
            border:1px solid var(--border); box-shadow:0 4px 24px rgba(27,58,45,.10);
            max-width:420px; width:100%;
            animation:rise .5s cubic-bezier(.22,1,.36,1) both;
        }
        @keyframes rise { from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);} }
        .icon-wrap {
            width:72px; height:72px; border-radius:20px; background:#fce8e6;
            display:flex; align-items:center; justify-content:center;
            margin:0 auto 20px; font-size:32px;
        }
        h1 { font-family:'Lora',serif; font-size:24px; font-weight:700; color:var(--forest); margin-bottom:8px; }
        p { font-size:14px; color:var(--muted); line-height:1.6; margin-bottom:28px; }
        .role-badge {
            display:inline-block; padding:3px 12px; border-radius:20px;
            font-size:12px; font-weight:700; letter-spacing:.5px;
            margin-bottom:24px;
        }
        a {
            display:inline-block; padding:11px 28px; background:var(--forest);
            color:#fff; border-radius:8px; text-decoration:none;
            font-size:14px; font-weight:600;
            transition:background .18s, transform .12s;
            box-shadow:0 3px 12px rgba(27,58,45,.20);
        }
        a:hover { background:#2D6147; transform:translateY(-1px); }
    </style>
</head>
<body>
<div class="box">
    <div class="icon-wrap">🚫</div>
    <h1>Access Denied</h1>

    @auth
    @php
        $roleColors = ['admin'=>'#C8922A','manager'=>'#1B3A2D','staff'=>'#8A7A65'];
        $rc = $roleColors[Auth::user()->role] ?? '#8A7A65';
    @endphp
    <span class="role-badge" style="background:{{ $rc }}22;color:{{ $rc }};">
        {{ strtoupper(Auth::user()->role ?? 'STAFF') }}
    </span>
    <p>You don't have permission to access this page.<br>This area is restricted based on your role.</p>
    @php
        $back = Auth::user()->role === 'staff' ? route('schedules.mine') : route('dashboard');
    @endphp
    <a href="{{ $back }}">← Go Back</a>
    @else
    <p>You are not logged in or do not have permission to view this page.</p>
    <a href="{{ route('login') }}">← Sign In</a>
    @endauth
</div>
</body>
</html>
