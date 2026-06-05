<?php

namespace App\Http\Controllers;

use App\Models\StaffRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StaffRecordController extends Controller
{
    // Get all staff records with their user info
    public function index()
    {
        // Load all staff records with their related user
        $records = StaffRecord::with('user')
            ->whereHas('user', function($query) {
                $query->where('role', 'staff');
            })
            ->latest()
            ->paginate(10);

        return view('staff.index', compact('records'));
    }

    // Add a new staff member
    public function store(Request $request)
    {
        // Validate all the input data
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

        // Hash the password
        $hashedPassword = Hash::make($data['password']);

        // Create the user account
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $hashedPassword,
            'role'     => 'staff',
        ]);

        // Get the salary or null if not provided
        $salary = isset($data['salary']) ? $data['salary'] : null;

        // Get the notes or null if not provided
        $notes = isset($data['notes']) ? $data['notes'] : null;

        // Create the staff record
        StaffRecord::create([
            'user_id'     => $user->id,
            'employee_id' => $data['employee_id'],
            'position'    => $data['position'],
            'department'  => $data['department'],
            'branch'      => $data['branch'],
            'hire_date'   => $data['hire_date'],
            'salary'      => $salary,
            'status'      => $data['status'],
            'notes'       => $notes,
        ]);

        return back()->with('toast_success', 'Staff member added successfully.');
    }

    // Update an existing staff record
    public function update(Request $request, StaffRecord $staffRecord)
    {
        // Validate the input
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

        // Update the related user if name or email is provided
        $userUpdate = [];

        if ($request->filled('name')) {
            $userUpdate['name'] = $request->name;
        }

        if ($request->filled('email')) {
            $userUpdate['email'] = $request->email;
        }

        // Update the user if we have changes
        if (count($userUpdate) > 0) {
            $staffRecord->user->update($userUpdate);
        }

        // Update the staff record
        $staffRecord->update($data);

        return back()->with('toast_success', 'Record updated successfully.');
    }

    // Delete a staff member
    public function destroy(StaffRecord $staffRecord)
    {
        // Get the related user
        $user = $staffRecord->user;

        // Delete the user (this also deletes the staff record)
        $user->delete();

        return back()->with('toast_success', 'Record and user account deleted.');
    }
}
