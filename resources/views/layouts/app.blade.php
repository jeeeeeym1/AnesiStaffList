<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Anesi Staff System')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@600;700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --forest: #1B3A2D; --forest-soft: #2D6147;
            --cream: #F5F0E8; --gold: #C8922A;
            --text: #1A1209; --muted: #8A7A65;
            --border: #DDD3C0; --sidebar-w: 240px;
        }
        body { font-family: 'DM Sans', sans-serif; background: #f0ebe0; min-height: 100vh; }
        /* Sidebar */
        .sidebar {
            width: var(--sidebar-w); background: var(--forest);
            min-height: 100vh; position: fixed; top: 0; left: 0;
            display: flex; flex-direction: column; z-index: 100;
            transition: transform .3s;
        }
        .sidebar-brand {
            padding: 20px 20px 16px;
            border-bottom: 1px solid rgba(255,255,255,.1);
        }
        .sidebar-brand .logo {
            width: 36px; height: 36px; background: var(--gold);
            border-radius: 10px; display: inline-flex;
            align-items: center; justify-content: center;
            font-family: 'Lora', serif; font-weight: 700;
            color: #fff; font-size: 16px; margin-right: 10px;
        }
        .sidebar-brand span { font-family: 'Lora', serif; font-weight: 700; color: #fff; font-size: 15px; }
        .sidebar-brand small { display: block; color: rgba(255,255,255,.5); font-size: 11px; margin-top: 2px; }
        .sidebar nav { flex: 1; padding: 16px 0; }
        .nav-label {
            font-size: 10px; font-weight: 600; letter-spacing: 1.5px;
            text-transform: uppercase; color: rgba(255,255,255,.35);
            padding: 12px 20px 4px;
        }
        .sidebar a {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 20px; color: rgba(255,255,255,.75);
            text-decoration: none; font-size: 14px; font-weight: 500;
            border-left: 3px solid transparent;
            transition: all .15s;
        }
        .sidebar a:hover, .sidebar a.active {
            color: #fff; background: rgba(255,255,255,.08);
            border-left-color: var(--gold);
        }
        .sidebar a i { font-size: 16px; width: 20px; text-align: center; }
        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid rgba(255,255,255,.1);
        }
        .sidebar-footer .user-info { display: flex; align-items: center; gap: 10px; }
        .sidebar-footer .avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background: var(--gold); display: flex; align-items: center;
            justify-content: center; color: #fff; font-weight: 700;
            font-size: 14px; overflow: hidden; flex-shrink: 0;
        }
        .sidebar-footer .avatar img { width: 100%; height: 100%; object-fit: cover; }
        .sidebar-footer .uname { font-size: 13px; font-weight: 600; color: #fff; }
        .sidebar-footer .urole { font-size: 11px; color: rgba(255,255,255,.45); }
        /* Main */
        .main-wrap { margin-left: var(--sidebar-w); min-height: 100vh; display: flex; flex-direction: column; }
        .topbar {
            background: #fff; border-bottom: 1px solid var(--border);
            padding: 12px 28px; display: flex; align-items: center;
            justify-content: space-between; position: sticky; top: 0; z-index: 50;
        }
        .topbar .page-title { font-family: 'Lora', serif; font-size: 18px; font-weight: 700; color: var(--text); }
        .topbar .topbar-right { display: flex; align-items: center; gap: 12px; }
        .content-area { padding: 28px; flex: 1; }
        /* Toast */
        .toast-container { position: fixed; top: 20px; right: 20px; z-index: 9999; }
        /* Cards */
        .stat-card {
            background: #fff; border-radius: 14px; padding: 20px 22px;
            border: 1px solid var(--border);
            box-shadow: 0 2px 12px rgba(27,58,45,.06);
        }
        .stat-card .stat-icon {
            width: 44px; height: 44px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; margin-bottom: 12px;
        }
        .stat-card .stat-val { font-size: 28px; font-weight: 700; color: var(--text); line-height: 1; }
        .stat-card .stat-lbl { font-size: 12px; color: var(--muted); margin-top: 4px; }
        /* Table card */
        .table-card {
            background: #fff; border-radius: 14px;
            border: 1px solid var(--border);
            box-shadow: 0 2px 12px rgba(27,58,45,.06);
            overflow: hidden;
        }
        .table-card-header {
            padding: 16px 20px; border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
        }
        .table-card-header h6 { font-family: 'Lora', serif; font-weight: 700; margin: 0; color: var(--text); }
        /* Btn overrides */
        .btn-forest { background: var(--forest); color: #fff; border: none; }
        .btn-forest:hover { background: var(--forest-soft); color: #fff; }
        .btn-gold { background: var(--gold); color: #fff; border: none; }
        .btn-gold:hover { background: #b07a1e; color: #fff; }
        /* Mobile toggle */
        .sidebar-toggle { display: none; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-wrap { margin-left: 0; }
            .sidebar-toggle { display: block; }
        }
    </style>
    @yield('styles')
</head>
<body>

<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="d-flex align-items-center">
            <div class="logo">À</div>
            <div>
                <span>Anesi</span>
                <small>Iced Coffee · Staff Portal</small>
            </div>
        </div>
    </div>

    <nav>
        {{-- Dashboard: admin only --}}
        @if(Auth::user()->role === 'admin')
        <div class="nav-label">Main</div>
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>
        @endif

        {{-- Admin: full user management --}}
        @if(Auth::user()->role === 'admin')
        <div class="nav-label">Administration</div>
        <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i> User Management
        </a>
        <a href="{{ route('staff.index') }}" class="{{ request()->routeIs('staff.*') ? 'active' : '' }}">
            <i class="bi bi-person-badge-fill"></i> Staff Records
        </a>
        @endif

        <div class="nav-label">Account</div>
        <a href="{{ route('profile.show') }}" class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <i class="bi bi-person-circle"></i> My Profile
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="avatar">
                @if(Auth::user()->avatar)
                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="">
                @else
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                @endif
            </div>
            <div>
                <div class="uname">{{ Auth::user()->name }}</div>
                <div class="urole">
                    @php
                        $roleColors = ['admin'=>'#C8922A','staff'=>'#8A7A65'];
                        $rc = $roleColors[Auth::user()->role] ?? '#8A7A65';
                    @endphp
                    <span style="background:{{ $rc }};color:#fff;font-size:10px;padding:1px 7px;border-radius:20px;font-weight:600;letter-spacing:.5px;">{{ strtoupper(Auth::user()->role ?? 'STAFF') }}</span>
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="mt-2">
            @csrf
            <button type="submit" class="btn btn-sm w-100" style="background:rgba(255,255,255,.1);color:rgba(255,255,255,.7);font-size:12px;">
                <i class="bi bi-box-arrow-left me-1"></i> Sign Out
            </button>
        </form>
    </div>
</div>

<div class="main-wrap">
    <div class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm sidebar-toggle" id="sidebarToggle" style="background:none;border:none;">
                <i class="bi bi-list fs-5"></i>
            </button>
            <span class="page-title">@yield('page-title', 'Dashboard')</span>
        </div>
        <div class="topbar-right">
            <span class="text-muted" style="font-size:13px;">{{ now()->format('D, M j Y') }}</span>
        </div>
    </div>

    <div class="content-area">
        @yield('content')
    </div>
</div>

{{-- Toast container --}}
<div class="toast-container">
    @foreach(['toast_success' => 'success', 'toast_error' => 'danger', 'toast_info' => 'info'] as $key => $type)
        @if(session($key))
        <div class="toast align-items-center text-bg-{{ $type }} border-0 show mb-2" role="alert" data-bs-autohide="true" data-bs-delay="4000">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-{{ $type === 'success' ? 'check-circle' : ($type === 'danger' ? 'x-circle' : 'info-circle') }}-fill me-2"></i>
                    {{ session($key) }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
        @endif
    @endforeach
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Auto-dismiss toasts
    document.querySelectorAll('.toast').forEach(el => new bootstrap.Toast(el).show());
    // Sidebar toggle
    document.getElementById('sidebarToggle')?.addEventListener('click', () => {
        document.getElementById('sidebar').classList.toggle('open');
    });
</script>
@yield('scripts')
</body>
</html>
