<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'username' => 'Invalid credentials',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    public function showRegister()
    {
        return view('login');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:3|max:50|regex:/^[\w\-\.@#$%^&*!]+$/|unique:users',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|max:70',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Debug the request data to see what's being received
        \Log::info('Registration request data:', $request->all());

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect('/');
    }

    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            
            // Debug the Google user data
            \Log::info('Google user data:', [
                'id' => $googleUser->id,
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'avatar' => $googleUser->avatar ?? null,
                'avatar_original' => $googleUser->avatar_original ?? null
            ]);
            
            // Get the best available avatar URL
            $avatarUrl = null;
            if (!empty($googleUser->avatar_original)) {
                $avatarUrl = $googleUser->avatar_original;
            } elseif (!empty($googleUser->avatar)) {
                $avatarUrl = $googleUser->avatar;
            }
            
            // Add size parameter to Google avatar URL if it exists
            if ($avatarUrl) {
                // Remove any existing query parameters
                $baseUrl = strtok($avatarUrl, '?');
                // Add size parameter and cache busting
                $avatarUrl = $baseUrl . '?sz=100&v=' . time();
                
                \Log::info('Using avatar URL: ' . $avatarUrl);
            }
            
            // Check if user exists in our database
            $existingUser = User::where('email', $googleUser->email)->first();
            
            if ($existingUser) {
                // Update existing user with latest avatar
                $existingUser->update([
                    'google_id' => $googleUser->id,
                    'avatar' => $avatarUrl,
                ]);
                
                // Log in existing user
                Auth::login($existingUser);
            } else {
                // Create a new user
                $newUser = User::create([
                    'username' => $this->generateUniqueUsername($googleUser->name),
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'password' => Hash::make(Str::random(16)), // Random secure password
                    'google_id' => $googleUser->id,
                    'avatar' => $avatarUrl,
                ]);
                
                Auth::login($newUser);
            }
            
            return redirect('/');
            
        } catch (\Exception $e) {
            \Log::error('Google login error: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Google login failed. Please try again.');
        }
    }
    
    /**
     * Generate a unique username based on the Google name
     */
    private function generateUniqueUsername($name)
    {
        // Convert name to lowercase and replace spaces with underscores
        $baseUsername = Str::slug($name, '_');
        $username = $baseUsername;
        $counter = 1;
        
        // Check if username exists, if so, append a number
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . '_' . $counter;
            $counter++;
        }
        
        return $username;
    }
}