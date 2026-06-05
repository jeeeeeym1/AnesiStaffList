<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Show the login page
    public function showLogin()
    {
        return view('auth.login');
    }

    // Handle user login
    public function login(Request $request)
    {
        // Check if email and password are provided
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Get remember value from checkbox
        $remember = $request->boolean('remember');

        // Try to login the user
        $loginSuccess = Auth::attempt($credentials, $remember);

        if ($loginSuccess) {
            // Create new session
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        // Login failed - go back with error message
        return back()->withErrors(['email' => 'auth'])->withInput($request->only('email', 'remember'));
    }

    // Show the register page
    public function showRegister()
    {
        return view('auth.register');
    }

    // Handle user registration
    public function register(Request $request)
    {
        // Validate form input
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role'     => 'required|in:admin,staff',
        ]);

        // Hash the password for security
        $hashedPassword = Hash::make($data['password']);

        // Create new user in database
        User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $hashedPassword,
            'role'     => $data['role'],
        ]);

        // Redirect to login page with success message
        return redirect()->route('login')->with('toast_success', 'Account created! Please sign in.');
    }

    // Handle user logout
    public function logout(Request $request)
    {
        // Logout the user
        Auth::logout();

        // Clear the session
        $request->session()->invalidate();

        // Create a new token to prevent attacks
        $request->session()->regenerateToken();

        // Redirect to login page
        return redirect()->route('login');
    }
}
