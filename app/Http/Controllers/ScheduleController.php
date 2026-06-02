<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\StaffRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        [$year, $mon] = explode('-', $month);

        $user = Auth::user();

        if ($user->role === 'admin') {
            // Admin sees ALL staff records (managers + staff)
            $staffRecords = StaffRecord::with('user')
                ->whereHas('user', fn($q) => $q->whereIn('role', ['manager', 'staff']))
                ->where('status', 'active')
                ->get()
                ->sortBy('user.name');

            $allowedIds = $staffRecords->pluck('id');
        } else {
            // Manager sees staff only
            $staffRecords = StaffRecord::with('user')
                ->whereHas('user', fn($q) => $q->where('role', 'staff'))
                ->where('status', 'active')
                ->get();

            $allowedIds = $staffRecords->pluck('id');
        }

        $schedules = Schedule::with(['staffRecord.user', 'creator'])
            ->whereIn('staff_record_id', $allowedIds)
            ->whereYear('schedule_date', $year)
            ->whereMonth('schedule_date', $mon)
            ->orderBy('schedule_date')
            ->orderBy('time_in')
            ->get();

        $grouped     = $schedules->groupBy(fn($s) => $s->schedule_date->format('Y-m-d'));
        $daysInMonth = (int) (new \DateTime("{$year}-{$mon}-01"))->format('t');
        $firstDay    = (int) (new \DateTime("{$year}-{$mon}-01"))->format('N');

        return view('schedules.index', compact(
            'schedules', 'grouped', 'staffRecords',
            'month', 'year', 'mon', 'daysInMonth', 'firstDay'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'staff_record_id' => 'required|exists:staff_records,id',
            'schedule_date'   => 'required|date',
            'time_in'         => 'required',
            'time_out'        => 'required|after:time_in',
            'shift'           => 'required|in:Morning,Afternoon',
            'notes'           => 'nullable|string|max:500',
        ]);

        $exists = Schedule::where('staff_record_id', $data['staff_record_id'])
            ->where('schedule_date', $data['schedule_date'])
            ->exists();

        if ($exists) {
            return back()->with('toast_error', 'This person already has a schedule on that date.');
        }

        $data['created_by'] = Auth::id();
        Schedule::create($data);

        return back()->with('toast_success', 'Schedule added successfully.');
    }

    public function update(Request $request, Schedule $schedule)
    {
        $data = $request->validate([
            'staff_record_id' => 'required|exists:staff_records,id',
            'schedule_date'   => 'required|date',
            'time_in'         => 'required',
            'time_out'        => 'required',
            'shift'           => 'required|in:Morning,Afternoon',
            'notes'           => 'nullable|string|max:500',
        ]);

        $schedule->update($data);

        return back()->with('toast_success', 'Schedule updated successfully.');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return back()->with('toast_success', 'Schedule deleted successfully.');
    }

    public function mySchedule(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        [$year, $mon] = explode('-', $month);

        $staffRecord = StaffRecord::where('user_id', Auth::id())->first();

        $schedules = collect();
        $grouped   = collect();
        $upcoming  = collect();

        if ($staffRecord) {
            $schedules = Schedule::with('creator')
                ->where('staff_record_id', $staffRecord->id)
                ->whereYear('schedule_date', $year)
                ->whereMonth('schedule_date', $mon)
                ->orderBy('schedule_date')
                ->get();

            $grouped = $schedules->groupBy(fn($s) => $s->schedule_date->format('Y-m-d'));

            $upcoming = Schedule::with('creator')
                ->where('staff_record_id', $staffRecord->id)
                ->whereBetween('schedule_date', [now()->toDateString(), now()->addDays(7)->toDateString()])
                ->orderBy('schedule_date')
                ->get();
        }

        $daysInMonth = (int) (new \DateTime("{$year}-{$mon}-01"))->format('t');
        $firstDay    = (int) (new \DateTime("{$year}-{$mon}-01"))->format('N');

        return view('schedules.mine', compact(
            'schedules', 'grouped', 'upcoming', 'staffRecord',
            'month', 'year', 'mon', 'daysInMonth', 'firstDay'
        ));
    }
}
