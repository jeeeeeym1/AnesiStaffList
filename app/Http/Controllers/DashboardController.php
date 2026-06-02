<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\StaffRecord;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Staff have no dashboard — send them to their schedule
        if ($user->role === 'staff') {
            return redirect()->route('schedules.mine');
        }

        if ($user->role === 'admin') {
            // Admin sees everything
            $totalUsers  = User::count();
            $totalStaff  = StaffRecord::count();
            $activeStaff = StaffRecord::where('status', 'active')->count();
            $onLeave     = StaffRecord::where('status', 'on_leave')->count();
            $totalManagers = User::where('role', 'manager')->count();
            $totalStaffUsers = User::where('role', 'staff')->count();

            $usersByMonth = User::select(
                DB::raw("DATE_FORMAT(created_at, '%b') as month"),
                DB::raw('COUNT(*) as count')
            )
                ->where('created_at', '>=', now()->subMonths(6))
                ->groupBy(DB::raw("DATE_FORMAT(created_at, '%b %Y')"), DB::raw("DATE_FORMAT(created_at, '%b')"))
                ->orderBy(DB::raw('MIN(created_at)'))
                ->pluck('count', 'month');

            $byDepartment = StaffRecord::select('department', DB::raw('COUNT(*) as count'))
                ->groupBy('department')
                ->pluck('count', 'department');

            $byStatus = StaffRecord::select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status');

            $byRole = User::select('role', DB::raw('COUNT(*) as count'))
                ->groupBy('role')
                ->pluck('count', 'role');

        } else {
            // Manager sees only their staff
            $managedIds = StaffRecord::whereHas('user', fn($q) => $q->where('role', 'staff'))
                ->pluck('id');

            $totalUsers      = 0;
            $totalManagers   = 0;
            $totalStaffUsers = User::where('role', 'staff')->count();
            $totalStaff      = $managedIds->count();
            $activeStaff     = StaffRecord::whereIn('id', $managedIds)->where('status', 'active')->count();
            $onLeave         = StaffRecord::whereIn('id', $managedIds)->where('status', 'on_leave')->count();

            $usersByMonth = User::select(
                DB::raw("DATE_FORMAT(created_at, '%b') as month"),
                DB::raw('COUNT(*) as count')
            )
                ->where('role', 'staff')
                ->where('created_at', '>=', now()->subMonths(6))
                ->groupBy(DB::raw("DATE_FORMAT(created_at, '%b %Y')"), DB::raw("DATE_FORMAT(created_at, '%b')"))
                ->orderBy(DB::raw('MIN(created_at)'))
                ->pluck('count', 'month');

            $byDepartment = StaffRecord::whereIn('id', $managedIds)
                ->select('department', DB::raw('COUNT(*) as count'))
                ->groupBy('department')
                ->pluck('count', 'department');

            $byStatus = StaffRecord::whereIn('id', $managedIds)
                ->select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status');

            $byRole = collect(['staff' => $totalStaff]);
        }

        // Upcoming schedules (next 7 days) — scoped by role
        $upcomingQuery = Schedule::with(['staffRecord.user'])
            ->whereBetween('schedule_date', [now()->toDateString(), now()->addDays(7)->toDateString()])
            ->orderBy('schedule_date');

        if ($user->role === 'manager') {
            $upcomingQuery->whereHas('staffRecord.user', fn($q) => $q->where('role', 'staff'));
        }

        $upcomingSchedules = $upcomingQuery->take(5)->get();

        return view('dashboard', compact(
            'totalUsers', 'totalStaff', 'activeStaff', 'onLeave',
            'totalManagers', 'totalStaffUsers',
            'usersByMonth', 'byDepartment', 'byStatus', 'byRole',
            'upcomingSchedules'
        ));
    }
}
