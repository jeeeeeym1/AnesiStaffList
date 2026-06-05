<?php

namespace App\Http\Controllers;

use App\Models\StaffRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Get all users with pagination
    public function index()
    {
        // Get all users with their staff records and show 10 per page
        $users = User::with('staffRecord')->latest()->paginate(10);

        return view('users.index', compact('users'));
    }

    // Create a new user
    public function store(Request $request)
    {
        // Validate all the input data
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users',
            'password'    => 'required|min:8',
            'role'        => 'required|in:admin,staff',
            // These fields are only required if role is staff
            'employee_id' => 'required_if:role,staff|nullable|string|unique:staff_records',
            'position'    => 'required_if:role,staff|nullable|string|max:255',
            'department'  => 'required_if:role,staff|nullable|string|max:255',
            'branch'      => 'required_if:role,staff|nullable|string|max:255',
            'hire_date'   => 'required_if:role,staff|nullable|date',
            'salary'      => 'nullable|numeric|min:0',
            'status'      => 'nullable|in:active,inactive,on_leave',
        ]);

        // Hash the password
        $hashedPassword = Hash::make($data['password']);

        // Create the user in database
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $hashedPassword,
            'role'     => $data['role'],
        ]);

        // If the role is staff, create a staff record
        if ($data['role'] === 'staff') {
            // Get salary or set to null
            $salary = isset($data['salary']) ? $data['salary'] : null;

            // Get status or set to active
            $status = isset($data['status']) ? $data['status'] : 'active';

            // Create staff record
            StaffRecord::create([
                'user_id'     => $user->id,
                'employee_id' => $data['employee_id'],
                'position'    => $data['position'],
                'department'  => $data['department'],
                'branch'      => $data['branch'],
                'hire_date'   => $data['hire_date'],
                'salary'      => $salary,
                'status'      => $status,
            ]);
        }

        // Show success message based on role
        $roleText = ucfirst($data['role']);
        return back()->with('toast_success', $roleText . ' account created successfully.');
    }

    // Update an existing user
    public function update(Request $request, User $user)
    {
        // Validate the input
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|in:admin,staff',
        ]);

        // Check if password is provided and update it
        if ($request->filled('password')) {
            // Validate password
            $request->validate(['password' => 'min:8']);

            // Hash and add to data
            $data['password'] = Hash::make($request->password);
        }

        // Update the user
        $user->update($data);

        return back()->with('toast_success', 'User updated successfully.');
    }

    // Delete a user
    public function destroy(User $user)
    {
        // Delete the user from database
        $user->delete();

        return back()->with('toast_success', 'User deleted successfully.');
    }
}
