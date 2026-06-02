<?php

namespace App\Http\Controllers;

use App\Models\StaffRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('staffRecord')->latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users',
            'password'    => 'required|min:8',
            'role'        => 'required|in:admin,staff',
            // employment fields — required only for staff
            'employee_id' => 'required_if:role,staff|nullable|string|unique:staff_records',
            'position'    => 'required_if:role,staff|nullable|string|max:255',
            'department'  => 'required_if:role,staff|nullable|string|max:255',
            'branch'      => 'required_if:role,staff|nullable|string|max:255',
            'hire_date'   => 'required_if:role,staff|nullable|date',
            'salary'      => 'nullable|numeric|min:0',
            'status'      => 'nullable|in:active,inactive,on_leave',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],
        ]);

        // Auto-create staff record for staff role only
        if ($data['role'] === 'staff') {
            StaffRecord::create([
                'user_id'     => $user->id,
                'employee_id' => $data['employee_id'],
                'position'    => $data['position'],
                'department'  => $data['department'],
                'branch'      => $data['branch'],
                'hire_date'   => $data['hire_date'],
                'salary'      => $data['salary'] ?? null,
                'status'      => $data['status'] ?? 'active',
            ]);
        }

        return back()->with('toast_success', ucfirst($data['role']) . ' account created successfully.');
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|in:admin,staff',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8']);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('toast_success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('toast_success', 'User deleted successfully.');
    }
}
