@extends('layouts.app')

@section('title', 'Scheduling — Anesi Staff System')
@section('page-title', 'Staff Scheduling')

@section('styles')
<style>
    .month-nav { display:flex; align-items:center; gap:12px; }
    .month-nav a {
        width:32px; height:32px; border-radius:8px; border:1px solid var(--border);
        background:#fff; display:flex; align-items:center; justify-content:center;
        color:var(--text); text-decoration:none; transition:all .15s;
    }
    .month-nav a:hover { background:var(--forest); color:#fff; border-color:var(--forest); }
    .month-label { font-family:'Lora',serif; font-size:18px; font-weight:700; color:var(--text); min-width:160px; text-align:center; }
    .cal-grid { display:grid; grid-template-columns:repeat(7,1fr); gap:6px; margin-top:16px; }
    .cal-day-header { text-align:center; font-size:11px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:var(--muted); padding:6px 0; }
    .cal-cell { background:#fff; border:1px solid var(--border); border-radius:10px; min-height:110px; padding:8px; position:relative; transition:box-shadow .15s; }
    .cal-cell:hover { box-shadow:0 2px 12px rgba(27,58,45,.10); }
    .cal-cell.empty { background:#f8f4ee; border-color:transparent; }
    .cal-cell.today { border-color:var(--gold); box-shadow:0 0 0 2px rgba(200,146,42,.2); }
    .cal-date { font-size:12px; font-weight:700; color:var(--muted); margin-bottom:6px; display:flex; align-items:center; justify-content:space-between; }
    .cal-date .add-btn {
        width:20px; height:20px; border-radius:6px; background:var(--forest);
        color:#fff; border:none; font-size:14px; line-height:1;
        display:flex; align-items:center; justify-content:center;
        cursor:pointer; opacity:0; transition:opacity .15s; padding:0;
    }
    .cal-cell:hover .add-btn { opacity:1; }
    .cal-date .today-dot { width:22px; height:22px; border-radius:50%; background:var(--forest); color:#fff; font-size:11px; font-weight:700; display:flex; align-items:center; justify-content:center; }
    .sched-chip { font-size:11px; border-radius:6px; padding:3px 7px; margin-bottom:3px; cursor:pointer; display:flex; align-items:center; justify-content:space-between; gap:4px; transition:opacity .15s; line-height:1.3; }
    .sched-chip:hover { opacity:.85; }
    .sched-chip .chip-name { font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:75px; }
    .sched-chip .chip-time { font-size:10px; opacity:.8; white-space:nowrap; }
    .chip-morning   { background:#e8f5e9; color:#2e7d32; }
    .chip-afternoon { background:#fff8e1; color:#f57f17; }
    /* Role distinction for admin view */
    .chip-manager-morning   { background:#e8f0eb; color:#1B3A2D; border-left:3px solid #C8922A; }
    .chip-manager-afternoon { background:#fdf3e3; color:#7a5200; border-left:3px solid #C8922A; }
    .shift-badge { font-size:11px; padding:2px 10px; border-radius:20px; font-weight:600; }
    .shift-morning   { background:#e8f5e9; color:#2e7d32; }
    .shift-afternoon { background:#fff8e1; color:#f57f17; }
    .role-pill { font-size:10px; padding:1px 7px; border-radius:20px; font-weight:700; letter-spacing:.3px; }
    .role-pill-manager { background:#fdf3e3; color:#C8922A; }
    .role-pill-staff   { background:#e8f0eb; color:#1B3A2D; }
    .view-toggle .btn { font-size:13px; }
    .view-toggle .btn.active { background:var(--forest); color:#fff; border-color:var(--forest); }
</style>
@endsection

@section('content')

@php
    $isAdmin   = Auth::user()->role === 'admin';
    $prevMonth = \Carbon\Carbon::createFromDate($year, $mon, 1)->subMonth()->format('Y-m');
    $nextMonth = \Carbon\Carbon::createFromDate($year, $mon, 1)->addMonth()->format('Y-m');
    $monthName = \Carbon\Carbon::createFromDate($year, $mon, 1)->format('F Y');
    // Group staff records by role for admin dropdown
    $managers = $staffRecords->filter(fn($sr) => $sr->user->role === 'manager');
    $staffs   = $staffRecords->filter(fn($sr) => $sr->user->role === 'staff');
@endphp

{{-- Top bar --}}
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div class="month-nav">
        <a href="{{ route('schedules.index', ['month' => $prevMonth]) }}"><i class="bi bi-chevron-left"></i></a>
        <span class="month-label">{{ $monthName }}</span>
        <a href="{{ route('schedules.index', ['month' => $nextMonth]) }}"><i class="bi bi-chevron-right"></i></a>
        <a href="{{ route('schedules.index') }}" class="ms-1" style="font-size:11px;padding:0 10px;width:auto;">Today</a>
    </div>
    <div class="d-flex gap-2 align-items-center">
        @if($isAdmin)
        <div class="d-flex gap-1">
            <span class="role-pill role-pill-manager align-self-center">Managers: {{ $schedules->filter(fn($s)=>$s->staffRecord->user->role==='manager')->count() }}</span>
            <span class="role-pill role-pill-staff align-self-center">Staff: {{ $schedules->filter(fn($s)=>$s->staffRecord->user->role==='staff')->count() }}</span>
        </div>
        @endif
        <div class="btn-group view-toggle" id="viewToggle">
            <button class="btn btn-sm btn-outline-secondary active" data-view="calendar"><i class="bi bi-calendar3 me-1"></i>Calendar</button>
            <button class="btn btn-sm btn-outline-secondary" data-view="list"><i class="bi bi-list-ul me-1"></i>List</button>
        </div>
        <button class="btn btn-sm btn-forest" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
            <i class="bi bi-plus-lg me-1"></i> Add Schedule
        </button>
    </div>
</div>

{{-- CALENDAR VIEW --}}
<div id="calendarView">
    <div class="table-card p-3">
        <div class="cal-grid">
            @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $d)
                <div class="cal-day-header">{{ $d }}</div>
            @endforeach

            @for($e = 1; $e < $firstDay; $e++)
                <div class="cal-cell empty"></div>
            @endfor

            @for($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $dateKey   = sprintf('%04d-%02d-%02d', $year, $mon, $day);
                    $isToday   = $dateKey === now()->format('Y-m-d');
                    $dayScheds = $grouped[$dateKey] ?? collect();
                @endphp
                <div class="cal-cell {{ $isToday ? 'today' : '' }}">
                    <div class="cal-date">
                        @if($isToday)
                            <span class="today-dot">{{ $day }}</span>
                        @else
                            <span>{{ $day }}</span>
                        @endif
                        <button class="add-btn" data-bs-toggle="modal" data-bs-target="#addScheduleModal"
                            data-date="{{ $dateKey }}" title="Add schedule">+</button>
                    </div>
                    @foreach($dayScheds as $sched)
                        @php
                            $srRole = $sched->staffRecord->user->role;
                            $shiftLower = strtolower($sched->shift);
                            $chipClass = $isAdmin && $srRole === 'manager'
                                ? 'chip-manager-'.$shiftLower
                                : 'chip-'.$shiftLower;
                        @endphp
                        <div class="sched-chip {{ $chipClass }}"
                            data-bs-toggle="modal" data-bs-target="#editScheduleModal"
                            data-id="{{ $sched->id }}"
                            data-staff="{{ $sched->staff_record_id }}"
                            data-date="{{ $sched->schedule_date->format('Y-m-d') }}"
                            data-timein="{{ $sched->time_in }}"
                            data-timeout="{{ $sched->time_out }}"
                            data-shift="{{ $sched->shift }}"
                            data-notes="{{ $sched->notes }}">
                            <span class="chip-name">{{ $sched->staffRecord->user->name }}</span>
                            <span class="chip-time">
                                {{ \Carbon\Carbon::parse($sched->time_in)->format('g:iA') }}
                                @if($isAdmin)
                                    <span style="margin-left:2px;opacity:.7;">{{ $srRole === 'manager' ? '👔' : '👤' }}</span>
                                @endif
                            </span>
                        </div>
                    @endforeach
                </div>
            @endfor
        </div>
    </div>
</div>

{{-- LIST VIEW --}}
<div id="listView" style="display:none;">
    <div class="table-card">
        <div class="table-card-header">
            <h6><i class="bi bi-calendar2-week-fill me-2"></i>{{ $monthName }} — All Schedules</h6>
            <span class="badge" style="background:#e8f0eb;color:#1B3A2D;">{{ $schedules->count() }} entries</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead style="background:#f8f4ee;">
                    <tr>
                        <th class="ps-3">Date</th>
                        <th>Name</th>
                        @if($isAdmin)<th>Role</th>@endif
                        <th>Position</th>
                        <th>Shift</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Scheduled By</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $sched)
                    @php $srRole = $sched->staffRecord->user->role; @endphp
                    <tr style="{{ $isAdmin && $srRole === 'manager' ? 'background:#fffdf7;' : '' }}">
                        <td class="ps-3">
                            <div style="font-weight:600;font-size:13px;">{{ $sched->schedule_date->format('D') }}</div>
                            <div style="font-size:12px;color:var(--muted);">{{ $sched->schedule_date->format('M d, Y') }}</div>
                        </td>
                        <td>
                            <div style="font-weight:600;">{{ $sched->staffRecord->user->name }}</div>
                            <div style="font-size:12px;color:var(--muted);">{{ $sched->staffRecord->employee_id }}</div>
                        </td>
                        @if($isAdmin)
                        <td>
                            <span class="role-pill {{ $srRole === 'manager' ? 'role-pill-manager' : 'role-pill-staff' }}">
                                {{ ucfirst($srRole) }}
                            </span>
                        </td>
                        @endif
                        <td style="font-size:13px;">{{ $sched->staffRecord->position }}</td>
                        <td><span class="shift-badge shift-{{ strtolower($sched->shift) }}">{{ $sched->shift }}</span></td>
                        <td style="font-size:13px;">{{ \Carbon\Carbon::parse($sched->time_in)->format('g:i A') }}</td>
                        <td style="font-size:13px;">{{ \Carbon\Carbon::parse($sched->time_out)->format('g:i A') }}</td>
                        <td style="font-size:12px;color:var(--muted);">{{ $sched->creator->name }}</td>
                        <td class="text-end pe-3">
                            <button class="btn btn-sm btn-outline-secondary me-1"
                                data-bs-toggle="modal" data-bs-target="#editScheduleModal"
                                data-id="{{ $sched->id }}"
                                data-staff="{{ $sched->staff_record_id }}"
                                data-date="{{ $sched->schedule_date->format('Y-m-d') }}"
                                data-timein="{{ $sched->time_in }}"
                                data-timeout="{{ $sched->time_out }}"
                                data-shift="{{ $sched->shift }}"
                                data-notes="{{ $sched->notes }}">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger"
                                data-bs-toggle="modal" data-bs-target="#deleteScheduleModal"
                                data-id="{{ $sched->id }}"
                                data-name="{{ $sched->staffRecord->user->name }}"
                                data-date="{{ $sched->schedule_date->format('M d, Y') }}">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="{{ $isAdmin ? 9 : 8 }}" class="text-center py-4 text-muted">No schedules for {{ $monthName }}.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ADD SCHEDULE MODAL --}}
<div class="modal fade" id="addScheduleModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('schedules.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="font-family:'Lora',serif;"><i class="bi bi-calendar-plus me-2"></i>Add Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Person to Schedule</label>
                        <select name="staff_record_id" class="form-select" required>
                            <option value="" disabled selected>— Select —</option>
                            @if($isAdmin && $managers->count())
                            <optgroup label="👔 Managers">
                                @foreach($managers as $sr)
                                    <option value="{{ $sr->id }}">{{ $sr->user->name }} — {{ $sr->position }} ({{ $sr->branch }})</option>
                                @endforeach
                            </optgroup>
                            @endif
                            @if($staffs->count())
                            <optgroup label="👤 Staff">
                                @foreach($staffs as $sr)
                                    <option value="{{ $sr->id }}">{{ $sr->user->name }} — {{ $sr->position }} ({{ $sr->branch }})</option>
                                @endforeach
                            </optgroup>
                            @endif
                            @if(!$isAdmin && !$staffs->count())
                                @foreach($staffRecords as $sr)
                                    <option value="{{ $sr->id }}">{{ $sr->user->name }} — {{ $sr->position }} ({{ $sr->branch }})</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Date</label>
                        <input type="date" name="schedule_date" id="addDate" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Shift</label>
                        <select name="shift" id="addShift" class="form-select" required>
                            <option value="Morning">☀️ Morning</option>
                            <option value="Afternoon">🌤️ Afternoon</option>
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Time In</label>
                            <input type="time" name="time_in" id="addTimeIn" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Time Out</label>
                            <input type="time" name="time_out" id="addTimeOut" class="form-control" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label fw-semibold">Notes <small class="text-muted fw-normal">(optional)</small></label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="e.g. Cover for absent staff..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-forest">Save Schedule</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- EDIT SCHEDULE MODAL --}}
<div class="modal fade" id="editScheduleModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" id="editScheduleForm">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="font-family:'Lora',serif;"><i class="bi bi-calendar-check me-2"></i>Edit Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Person</label>
                        <select name="staff_record_id" id="eStaff" class="form-select" required>
                            @if($isAdmin && $managers->count())
                            <optgroup label="👔 Managers">
                                @foreach($managers as $sr)
                                    <option value="{{ $sr->id }}">{{ $sr->user->name }} — {{ $sr->position }}</option>
                                @endforeach
                            </optgroup>
                            @endif
                            @if($staffs->count())
                            <optgroup label="👤 Staff">
                                @foreach($staffs as $sr)
                                    <option value="{{ $sr->id }}">{{ $sr->user->name }} — {{ $sr->position }}</option>
                                @endforeach
                            </optgroup>
                            @endif
                            @if(!$isAdmin && !$staffs->count())
                                @foreach($staffRecords as $sr)
                                    <option value="{{ $sr->id }}">{{ $sr->user->name }} — {{ $sr->position }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Date</label>
                        <input type="date" name="schedule_date" id="eDate" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Shift</label>
                        <select name="shift" id="eShift" class="form-select" required>
                            <option value="Morning">☀️ Morning</option>
                            <option value="Afternoon">🌤️ Afternoon</option>
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Time In</label>
                            <input type="time" name="time_in" id="eTimeIn" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Time Out</label>
                            <input type="time" name="time_out" id="eTimeOut" class="form-control" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" id="eNotes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-forest">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- DELETE SCHEDULE MODAL --}}
<div class="modal fade" id="deleteScheduleModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <form method="POST" id="deleteScheduleForm">
            @csrf @method('DELETE')
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-danger" style="font-family:'Lora',serif;">Remove Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-0">
                    <p class="mb-0">Remove schedule for <strong id="delName"></strong> on <strong id="delDate"></strong>?</p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
const shiftPresets = { Morning: { in:'06:00', out:'14:00' }, Afternoon: { in:'14:00', out:'22:00' } };

document.getElementById('addShift').addEventListener('change', function() {
    const p = shiftPresets[this.value];
    if (p) { document.getElementById('addTimeIn').value = p.in; document.getElementById('addTimeOut').value = p.out; }
});

document.getElementById('addScheduleModal').addEventListener('show.bs.modal', function(e) {
    const d = e.relatedTarget?.dataset?.date;
    if (d) document.getElementById('addDate').value = d;
});

document.getElementById('editScheduleModal').addEventListener('show.bs.modal', function(e) {
    const d = e.relatedTarget.dataset;
    document.getElementById('editScheduleForm').action = `/schedules/${d.id}`;
    document.getElementById('eStaff').value   = d.staff;
    document.getElementById('eDate').value    = d.date;
    document.getElementById('eShift').value   = d.shift;
    document.getElementById('eTimeIn').value  = d.timein;
    document.getElementById('eTimeOut').value = d.timeout;
    document.getElementById('eNotes').value   = d.notes || '';
});

document.getElementById('deleteScheduleModal').addEventListener('show.bs.modal', function(e) {
    const d = e.relatedTarget.dataset;
    document.getElementById('deleteScheduleForm').action = `/schedules/${d.id}`;
    document.getElementById('delName').textContent = d.name;
    document.getElementById('delDate').textContent = d.date;
});

document.querySelectorAll('#viewToggle .btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('#viewToggle .btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        const v = this.dataset.view;
        document.getElementById('calendarView').style.display = v === 'calendar' ? '' : 'none';
        document.getElementById('listView').style.display     = v === 'list'     ? '' : 'none';
    });
});
</script>
@endsection
