<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    // Show the current user's profile
    public function show()
    {
        // Get the logged-in user
        $user = Auth::user();

        // Return the profile view
        return view('profile.show', ['user' => $user]);
    }

    // Update the current user's profile
    public function update(Request $request)
    {
        // Get the logged-in user
        $user = Auth::user();

        // Validate the input data
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email,' . $user->id,
            'gender'  => 'nullable|string',
            'address' => 'nullable|string|max:500',
            'phone'   => 'nullable|string|max:20',
        ]);

        // Check if password is provided and update it
        if ($request->filled('password')) {
            // Validate that password matches the confirmation
            $request->validate(['password' => 'min:8|confirmed']);

            // Hash and add to data
            $data['password'] = Hash::make($request->password);
        }

        // Check if avatar file is uploaded
        if ($request->hasFile('avatar')) {
            // Validate the image file
            $request->validate(['avatar' => 'image|max:2048']);

            // Store the image in the public storage
            $filePath = $request->file('avatar')->store('avatars', 'public');

            // Add avatar path to data
            $data['avatar'] = $filePath;
        }

        // Update the user with new data
        $user->update($data);

        // Return with success message
        return back()->with('toast_success', 'Profile updated successfully.');
    }
}
