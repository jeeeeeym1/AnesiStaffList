@extends('layouts.app')

@section('title', 'Dashboard — Anesi Staff System')
@section('page-title', 'Dashboard')

@section('content')

@php $role = Auth::user()->role; @endphp

{{-- ── STAT CARDS ── --}}
<div class="row g-3 mb-4">
    @if($role === 'admin')
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e8f0eb;color:#1B3A2D;"><i class="bi bi-people-fill"></i></div>
            <div class="stat-val">{{ $totalUsers }}</div>
            <div class="stat-lbl">Total Users</div>
        </div>
    </div>
    @endif

    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e6f4ea;color:#2e7d32;"><i class="bi bi-person-check-fill"></i></div>
            <div class="stat-val">{{ $activeStaff }}</div>
            <div class="stat-lbl">Active Staff</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fff3e0;color:#e65100;"><i class="bi bi-person-dash-fill"></i></div>
            <div class="stat-val">{{ $onLeave }}</div>
            <div class="stat-lbl">On Leave</div>
        </div>
    </div>

    @if($role === 'admin')
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e8f0eb;color:#1B3A2D;"><i class="bi bi-person-badge-fill"></i></div>
            <div class="stat-val">{{ $totalStaffUsers }}</div>
            <div class="stat-lbl">Total Staff</div>
        </div>
    </div>
    @endif
</div>

{{-- ── CHARTS ── --}}
<div class="row g-3 mb-4">
    <div class="col-md-7">
        <div class="table-card p-3">
            <div class="table-card-header px-0 pt-0">
                <h6>
                    <i class="bi bi-bar-chart-fill me-2" style="color:#1B3A2D;"></i>
                    User Registrations (Last 6 Months)
                </h6>
            </div>
            <canvas id="usersChart" height="120"></canvas>
        </div>
    </div>
    <div class="col-md-5">
        <div class="table-card p-3">
            <div class="table-card-header px-0 pt-0">
                <h6><i class="bi bi-pie-chart-fill me-2" style="color:#C8922A;"></i>Staff by Status</h6>
            </div>
            <canvas id="statusChart" height="160"></canvas>
        </div>
    </div>

    @if($role === 'admin')
    <div class="col-md-5">
        <div class="table-card p-3">
            <div class="table-card-header px-0 pt-0">
                <h6><i class="bi bi-pie-chart-fill me-2" style="color:#1B3A2D;"></i>Users by Role</h6>
            </div>
            <canvas id="roleChart" height="160"></canvas>
        </div>
    </div>
    @endif

    <div class="col-md-7">
        <div class="table-card p-3">
            <div class="table-card-header px-0 pt-0">
                <h6><i class="bi bi-diagram-3-fill me-2" style="color:#1B3A2D;"></i>Staff by Department</h6>
            </div>
            <canvas id="deptChart" height="80"></canvas>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
const forest = '#1B3A2D', gold = '#C8922A', soft = '#2D6147', muted = '#8A7A65';

new Chart(document.getElementById('usersChart'), {
    type: 'bar',
    data: {
        labels: @json($usersByMonth->keys()),
        datasets: [{ label: 'Count', data: @json($usersByMonth->values()), backgroundColor: forest, borderRadius: 6 }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
});

new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: @json($byStatus->keys()->map(fn($k) => ucfirst(str_replace('_',' ',$k)))),
        datasets: [{ data: @json($byStatus->values()), backgroundColor: [forest, gold, soft, muted], borderWidth: 2, borderColor: '#fff' }]
    },
    options: { plugins: { legend: { position: 'bottom' } }, cutout: '65%' }
});

new Chart(document.getElementById('deptChart'), {
    type: 'bar',
    data: {
        labels: @json($byDepartment->keys()),
        datasets: [{ label: 'Staff', data: @json($byDepartment->values()), backgroundColor: gold, borderRadius: 6 }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
});

@if(Auth::user()->role === 'admin')
new Chart(document.getElementById('roleChart'), {
    type: 'doughnut',
    data: {
        labels: @json($byRole->keys()->map(fn($k) => ucfirst($k))),
        datasets: [{ data: @json($byRole->values()), backgroundColor: [gold, forest, soft, muted], borderWidth: 2, borderColor: '#fff' }]
    },
    options: { plugins: { legend: { position: 'bottom' } }, cutout: '65%' }
});
@endif
</script>
@endsection
