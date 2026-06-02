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
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fdf3e3;color:#C8922A;"><i class="bi bi-person-gear"></i></div>
            <div class="stat-val">{{ $totalManagers }}</div>
            <div class="stat-lbl">Managers</div>
        </div>
    </div>
    @endif

    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e6f4ea;color:#2e7d32;"><i class="bi bi-person-check-fill"></i></div>
            <div class="stat-val">{{ $activeStaff }}</div>
            <div class="stat-lbl">Active {{ $role === 'admin' ? 'Staff' : 'Staff' }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fff3e0;color:#e65100;"><i class="bi bi-person-dash-fill"></i></div>
            <div class="stat-val">{{ $onLeave }}</div>
            <div class="stat-lbl">On Leave</div>
        </div>
    </div>

    @if($role === 'manager')
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#e8f0eb;color:#1B3A2D;"><i class="bi bi-person-badge-fill"></i></div>
            <div class="stat-val">{{ $totalStaff }}</div>
            <div class="stat-lbl">Total Staff</div>
        </div>
    </div>
    @endif

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
                    {{ $role === 'admin' ? 'User Registrations' : 'Staff Added' }} (Last 6 Months)
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

    <div class="{{ $role === 'admin' ? 'col-md-7' : 'col-12' }}">
        <div class="table-card p-3">
            <div class="table-card-header px-0 pt-0">
                <h6><i class="bi bi-diagram-3-fill me-2" style="color:#1B3A2D;"></i>Staff by Department</h6>
            </div>
            <canvas id="deptChart" height="80"></canvas>
        </div>
    </div>
</div>

{{-- ── UPCOMING SCHEDULES ── --}}
@if($upcomingSchedules->count())
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="bi bi-calendar-week-fill me-2" style="color:#C8922A;"></i>Upcoming Schedules (Next 7 Days)</h6>
        <a href="{{ route('schedules.index') }}" class="btn btn-sm btn-forest">View All</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead style="background:#f8f4ee;">
                <tr>
                    <th class="ps-3">Date</th>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Shift</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach($upcomingSchedules as $s)
                @php $isToday = $s->schedule_date->format('Y-m-d') === now()->toDateString(); @endphp
                <tr style="{{ $isToday ? 'background:#fffbf0;' : '' }}">
                    <td class="ps-3">
                        <div style="font-weight:600;font-size:13px;">
                            {{ $s->schedule_date->format('M d') }}
                            @if($isToday)<span class="badge ms-1" style="background:#C8922A;color:#fff;font-size:10px;">Today</span>@endif
                        </div>
                        <div style="font-size:11px;color:#8A7A65;">{{ $s->schedule_date->format('l') }}</div>
                    </td>
                    <td style="font-weight:500;">{{ $s->staffRecord->user->name }}</td>
                    <td style="font-size:13px;color:#8A7A65;">{{ $s->staffRecord->position }}</td>
                    <td>
                        @php $sc = $s->shift === 'Morning' ? '#e8f5e9:#2e7d32' : '#fff8e1:#f57f17'; [$bg,$fg] = explode(':', $sc); @endphp
                        <span class="badge" style="background:{{ $bg }};color:{{ $fg }};">{{ $s->shift }}</span>
                    </td>
                    <td style="font-size:13px;">
                        {{ \Carbon\Carbon::parse($s->time_in)->format('g:i A') }} – {{ \Carbon\Carbon::parse($s->time_out)->format('g:i A') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

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
