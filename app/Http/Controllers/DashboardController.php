<?php

namespace App\Http\Controllers;

use App\Models\StaffRecord;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Only admin sees dashboard
        if ($user->role !== 'admin') {
            return redirect()->route('profile.show');
        }

        // Admin sees everything
        $totalUsers  = User::count();
        $totalStaff  = StaffRecord::count();
        $activeStaff = StaffRecord::where('status', 'active')->count();
        $onLeave     = StaffRecord::where('status', 'on_leave')->count();
        $totalAdmins = User::where('role', 'admin')->count();
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

        return view('dashboard', compact(
            'totalUsers', 'totalStaff', 'activeStaff', 'onLeave',
            'totalAdmins', 'totalStaffUsers',
            'usersByMonth', 'byDepartment', 'byStatus', 'byRole'
        ));
    }
}
