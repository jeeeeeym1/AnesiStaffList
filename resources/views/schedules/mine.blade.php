@extends('layouts.app')

@section('title', 'My Schedule — Anesi Staff System')
@section('page-title', 'My Schedule')

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

    /* Upcoming cards */
    .upcoming-card {
        background:#fff; border:1px solid var(--border); border-radius:12px;
        padding:14px 16px; display:flex; align-items:center; gap:14px;
    }
    .upcoming-date-box {
        width:48px; height:52px; border-radius:10px; background:var(--forest);
        color:#fff; display:flex; flex-direction:column;
        align-items:center; justify-content:center; flex-shrink:0;
    }
    .upcoming-date-box .day-num { font-size:20px; font-weight:700; line-height:1; }
    .upcoming-date-box .day-name { font-size:10px; opacity:.7; text-transform:uppercase; letter-spacing:.5px; }
    .upcoming-card.today-card { border-color:var(--gold); box-shadow:0 0 0 2px rgba(200,146,42,.2); }
    .upcoming-card.today-card .upcoming-date-box { background:var(--gold); }

    /* Calendar */
    .cal-grid {
        display:grid; grid-template-columns:repeat(7,1fr); gap:5px; margin-top:12px;
    }
    .cal-day-header {
        text-align:center; font-size:11px; font-weight:700;
        letter-spacing:1px; text-transform:uppercase; color:var(--muted); padding:6px 0;
    }
    .cal-cell {
        background:#fff; border:1px solid var(--border); border-radius:10px;
        min-height:80px; padding:7px; position:relative;
    }
    .cal-cell.empty { background:#f8f4ee; border-color:transparent; }
    .cal-cell.today { border-color:var(--gold); box-shadow:0 0 0 2px rgba(200,146,42,.18); }
    .cal-cell.has-shift { background:#f0f7f2; border-color:#a5d6b0; }
    .cal-date {
        font-size:11px; font-weight:700; color:var(--muted); margin-bottom:5px;
    }
    .cal-date .today-dot {
        width:20px; height:20px; border-radius:50%; background:var(--forest);
        color:#fff; font-size:10px; font-weight:700;
        display:inline-flex; align-items:center; justify-content:center;
    }
    .sched-chip {
        font-size:10px; border-radius:5px; padding:2px 6px;
        margin-bottom:2px; display:block; font-weight:600; line-height:1.4;
    }
    .chip-morning   { background:#e8f5e9; color:#2e7d32; }
    .chip-afternoon { background:#fff8e1; color:#f57f17; }

    .shift-badge { font-size:11px; padding:2px 10px; border-radius:20px; font-weight:600; }
    .shift-morning   { background:#e8f5e9; color:#2e7d32; }
    .shift-afternoon { background:#fff8e1; color:#f57f17; }

    .no-record-box {
        background:#fff; border:2px dashed var(--border); border-radius:14px;
        padding:48px 24px; text-align:center;
    }
</style>
@endsection

@section('content')

@if(!$staffRecord)
{{-- No staff record linked --}}
<div class="no-record-box">
    <i class="bi bi-calendar-x" style="font-size:48px;color:var(--border);"></i>
    <h5 class="mt-3" style="font-family:'Lora',serif;">No Staff Record Found</h5>
    <p class="text-muted mb-0" style="font-size:14px;">
        Your account is not linked to a staff record yet.<br>
        Please contact your Admin or Manager to set up your profile.
    </p>
</div>

@else

@php
    $prevMonth = \Carbon\Carbon::createFromDate($year, $mon, 1)->subMonth()->format('Y-m');
    $nextMonth = \Carbon\Carbon::createFromDate($year, $mon, 1)->addMonth()->format('Y-m');
    $monthName = \Carbon\Carbon::createFromDate($year, $mon, 1)->format('F Y');
    $today     = now()->toDateString();
@endphp

{{-- Staff info banner --}}
<div class="table-card p-3 mb-4 d-flex align-items-center gap-3" style="background:linear-gradient(135deg,#1B3A2D,#2D6147);border:none;">
    <div style="width:52px;height:52px;border-radius:50%;background:var(--gold);color:#fff;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:700;flex-shrink:0;overflow:hidden;">
        @if(Auth::user()->avatar)
            <img src="{{ asset('storage/'.Auth::user()->avatar) }}" style="width:100%;height:100%;object-fit:cover;">
        @else
            {{ strtoupper(substr(Auth::user()->name,0,1)) }}
        @endif
    </div>
    <div>
        <div style="font-family:'Lora',serif;font-size:16px;font-weight:700;color:#fff;">{{ Auth::user()->name }}</div>
        <div style="font-size:12px;color:rgba(255,255,255,.65);">
            {{ $staffRecord->position }} &nbsp;·&nbsp; {{ $staffRecord->department }} &nbsp;·&nbsp; {{ $staffRecord->branch }}
            &nbsp;·&nbsp; <code style="color:rgba(255,255,255,.5);font-size:11px;">{{ $staffRecord->employee_id }}</code>
        </div>
    </div>
    <div class="ms-auto text-end">
        <div style="font-size:11px;color:rgba(255,255,255,.5);">Shifts this month</div>
        <div style="font-size:28px;font-weight:700;color:#fff;line-height:1;">{{ $schedules->count() }}</div>
    </div>
</div>

{{-- Upcoming shifts (next 7 days) --}}
@if($upcoming->count())
<div class="mb-4">
    <div style="font-family:'Lora',serif;font-size:15px;font-weight:700;color:var(--text);margin-bottom:12px;">
        <i class="bi bi-lightning-charge-fill me-2" style="color:var(--gold);"></i>Upcoming Shifts (Next 7 Days)
    </div>
    <div class="row g-2">
        @foreach($upcoming as $s)
        @php $isToday = $s->schedule_date->format('Y-m-d') === $today; @endphp
        <div class="col-md-4 col-sm-6">
            <div class="upcoming-card {{ $isToday ? 'today-card' : '' }}">
                <div class="upcoming-date-box">
                    <span class="day-num">{{ $s->schedule_date->format('d') }}</span>
                    <span class="day-name">{{ $s->schedule_date->format('D') }}</span>
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-weight:600;font-size:13px;color:var(--text);">
                        {{ $isToday ? '🟢 Today' : $s->schedule_date->format('M d, Y') }}
                    </div>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <span class="shift-badge shift-{{ strtolower($s->shift) }}">{{ $s->shift }}</span>
                        <span style="font-size:12px;color:var(--muted);">
                            {{ \Carbon\Carbon::parse($s->time_in)->format('g:i A') }} – {{ \Carbon\Carbon::parse($s->time_out)->format('g:i A') }}
                        </span>
                    </div>
                    @if($s->notes)
                    <div style="font-size:11px;color:var(--muted);margin-top:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        <i class="bi bi-chat-left-text me-1"></i>{{ $s->notes }}
                    </div>
                    @endif
                    <div style="font-size:11px;color:var(--muted);margin-top:2px;">
                        <i class="bi bi-person-fill me-1"></i>Assigned by {{ $s->creator->name }}
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Month calendar --}}
<div class="table-card p-3 mb-4">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <div class="month-nav">
            <a href="{{ route('schedules.mine', ['month' => $prevMonth]) }}"><i class="bi bi-chevron-left"></i></a>
            <span class="month-label">{{ $monthName }}</span>
            <a href="{{ route('schedules.mine', ['month' => $nextMonth]) }}"><i class="bi bi-chevron-right"></i></a>
            <a href="{{ route('schedules.mine') }}" style="font-size:11px;padding:0 10px;width:auto;">Today</a>
        </div>
        <div style="font-size:12px;color:var(--muted);">
            <span class="sched-chip chip-morning d-inline-block me-1">Morning</span>
            <span class="sched-chip chip-afternoon d-inline-block">Afternoon</span>
        </div>
    </div>

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
                $isToday   = $dateKey === $today;
                $dayScheds = $grouped[$dateKey] ?? collect();
            @endphp
            <div class="cal-cell {{ $isToday ? 'today' : '' }} {{ $dayScheds->count() ? 'has-shift' : '' }}">
                <div class="cal-date">
                    @if($isToday)
                        <span class="today-dot">{{ $day }}</span>
                    @else
                        {{ $day }}
                    @endif
                </div>
                @foreach($dayScheds as $sched)
                    <span class="sched-chip chip-{{ strtolower($sched->shift) }}"
                        title="{{ $sched->shift }}: {{ \Carbon\Carbon::parse($sched->time_in)->format('g:iA') }} – {{ \Carbon\Carbon::parse($sched->time_out)->format('g:iA') }}{{ $sched->notes ? ' | '.$sched->notes : '' }}">
                        {{ \Carbon\Carbon::parse($sched->time_in)->format('g:iA') }}
                    </span>
                @endforeach
            </div>
        @endfor
    </div>
</div>

{{-- Full month list --}}
<div class="table-card">
    <div class="table-card-header">
        <h6><i class="bi bi-list-check me-2"></i>{{ $monthName }} — Full Schedule</h6>
        <span class="badge" style="background:#e8f0eb;color:#1B3A2D;">{{ $schedules->count() }} shifts</span>
    </div>
    @if($schedules->count())
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead style="background:#f8f4ee;">
                <tr>
                    <th class="ps-3">Date</th>
                    <th>Day</th>
                    <th>Shift</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Hours</th>
                    <th>Assigned By</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($schedules as $sched)
                @php
                    $tin  = \Carbon\Carbon::parse($sched->schedule_date->format('Y-m-d').' '.$sched->time_in);
                    $tout = \Carbon\Carbon::parse($sched->schedule_date->format('Y-m-d').' '.$sched->time_out);
                    if ($tout->lt($tin)) $tout->addDay(); // overnight shift
                    $hours = $tin->diffInMinutes($tout) / 60;
                    $isToday = $sched->schedule_date->format('Y-m-d') === $today;
                @endphp
                <tr style="{{ $isToday ? 'background:#fffbf0;' : '' }}">
                    <td class="ps-3">
                        <div style="font-weight:600;font-size:13px;">
                            {{ $sched->schedule_date->format('M d, Y') }}
                            @if($isToday)<span class="badge ms-1" style="background:var(--gold);color:#fff;font-size:10px;">Today</span>@endif
                        </div>
                    </td>
                    <td style="font-size:13px;color:var(--muted);">{{ $sched->schedule_date->format('l') }}</td>
                    <td><span class="shift-badge shift-{{ strtolower($sched->shift) }}">{{ $sched->shift }}</span></td>
                    <td style="font-size:13px;">{{ \Carbon\Carbon::parse($sched->time_in)->format('g:i A') }}</td>
                    <td style="font-size:13px;">{{ \Carbon\Carbon::parse($sched->time_out)->format('g:i A') }}</td>
                    <td style="font-size:13px;font-weight:600;color:var(--forest);">{{ number_format($hours, 1) }}h</td>
                    <td style="font-size:12px;color:var(--muted);">{{ $sched->creator->name }}</td>
                    <td style="font-size:12px;color:var(--muted);">{{ $sched->notes ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot style="background:#f8f4ee;">
                <tr>
                    <td colspan="5" class="ps-3 fw-semibold" style="font-size:13px;">Total Hours</td>
                    <td style="font-size:13px;font-weight:700;color:var(--forest);">
                        @php
                            $total = $schedules->sum(function($s) {
                                $tin  = \Carbon\Carbon::parse($s->schedule_date->format('Y-m-d').' '.$s->time_in);
                                $tout = \Carbon\Carbon::parse($s->schedule_date->format('Y-m-d').' '.$s->time_out);
                                if ($tout->lt($tin)) $tout->addDay();
                                return $tin->diffInMinutes($tout) / 60;
                            });
                        @endphp
                        {{ number_format($total, 1) }}h
                    </td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    </div>
    @else
    <div class="text-center py-5 text-muted">
        <i class="bi bi-calendar2-x" style="font-size:36px;opacity:.3;"></i>
        <p class="mt-2 mb-0" style="font-size:14px;">No shifts scheduled for {{ $monthName }}.</p>
    </div>
    @endif
</div>

@endif
@endsection
