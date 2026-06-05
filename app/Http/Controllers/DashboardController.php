<?php

namespace App\Http\Controllers;

use App\Models\StaffRecord;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // Show the admin dashboard with statistics
    public function index()
    {
        // Get the current logged-in user
        $user = Auth::user();

        // Check if user is admin, if not redirect to profile
        if ($user->role !== 'admin') {
            return redirect()->route('profile.show');
        }

        // Count total users
        $totalUsers = User::count();

        // Count total staff records
        $totalStaff = StaffRecord::count();

        // Count active staff members
        $activeStaff = StaffRecord::where('status', 'active')->count();

        // Count staff members on leave
        $onLeave = StaffRecord::where('status', 'on_leave')->count();

        // Count total admins
        $totalAdmins = User::where('role', 'admin')->count();

        // Count total staff users
        $totalStaffUsers = User::where('role', 'staff')->count();

        // Get users created in the last 6 months grouped by month
        $usersByMonth = User::select(
            DB::raw("DATE_FORMAT(created_at, '%b') as month"),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%b %Y')"), DB::raw("DATE_FORMAT(created_at, '%b')"))
            ->orderBy(DB::raw('MIN(created_at)'))
            ->pluck('count', 'month');

        // Get staff count by department
        $byDepartment = StaffRecord::select('department', DB::raw('COUNT(*) as count'))
            ->groupBy('department')
            ->pluck('count', 'department');

        // Get staff count by status
        $byStatus = StaffRecord::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        // Get user count by role
        $byRole = User::select('role', DB::raw('COUNT(*) as count'))
            ->groupBy('role')
            ->pluck('count', 'role');

        // Return the dashboard view with all statistics
        return view('dashboard', compact(
            'totalUsers', 'totalStaff', 'activeStaff', 'onLeave',
            'totalAdmins', 'totalStaffUsers',
            'usersByMonth', 'byDepartment', 'byStatus', 'byRole'
        ));
    }
}
