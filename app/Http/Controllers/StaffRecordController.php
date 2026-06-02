<?php

namespace App\Http\Controllers;

use App\Models\StaffRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StaffRecordController extends Controller
{
    public function index()
    {
        $authUser = Auth::user();

        // Admin sees manager records; Manager sees staff records
        if ($authUser->role === 'admin') {
            $records = StaffRecord::with('user')
                ->whereHas('user', fn($q) => $q->where('role', 'manager'))
                ->latest()->paginate(10);
        } else {
            $records = StaffRecord::with('user')
                ->whereHas('user', fn($q) => $q->where('role', 'staff'))
                ->latest()->paginate(10);
        }

        return view('staff.index', compact('records', 'authUser'));
    }

    public function store(Request $request)
    {
        $authUser   = Auth::user();
        $targetRole = $authUser->role === 'admin' ? 'manager' : 'staff';

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|min:8',
            'employee_id' => 'required|string|unique:staff_records',
            'position'    => 'required|string|max:255',
            'department'  => 'required|string|max:255',
            'branch'      => 'required|string|max:255',
            'hire_date'   => 'required|date',
            'salary'      => 'nullable|numeric|min:0',
            'status'      => 'required|in:active,inactive,on_leave',
            'notes'       => 'nullable|string',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $targetRole,
        ]);

        StaffRecord::create([
            'user_id'     => $user->id,
            'employee_id' => $data['employee_id'],
            'position'    => $data['position'],
            'department'  => $data['department'],
            'branch'      => $data['branch'],
            'hire_date'   => $data['hire_date'],
            'salary'      => $data['salary'] ?? null,
            'status'      => $data['status'],
            'notes'       => $data['notes'] ?? null,
        ]);

        return back()->with('toast_success', ucfirst($targetRole) . ' added successfully.');
    }

    public function update(Request $request, StaffRecord $staffRecord)
    {
        $data = $request->validate([
            'employee_id' => 'required|string|unique:staff_records,employee_id,' . $staffRecord->id,
            'position'    => 'required|string|max:255',
            'department'  => 'required|string|max:255',
            'branch'      => 'required|string|max:255',
            'hire_date'   => 'required|date',
            'salary'      => 'nullable|numeric|min:0',
            'status'      => 'required|in:active,inactive,on_leave',
            'notes'       => 'nullable|string',
        ]);

        // Also allow updating the linked user's name/email
        if ($request->filled('name') || $request->filled('email')) {
            $userUpdate = [];
            if ($request->filled('name'))  $userUpdate['name']  = $request->name;
            if ($request->filled('email')) $userUpdate['email'] = $request->email;
            $staffRecord->user->update($userUpdate);
        }

        $staffRecord->update($data);

        return back()->with('toast_success', 'Record updated successfully.');
    }

    public function destroy(StaffRecord $staffRecord)
    {
        $staffRecord->user->delete(); // cascades to staff_record
        return back()->with('toast_success', 'Record and user account deleted.');
    }
}
