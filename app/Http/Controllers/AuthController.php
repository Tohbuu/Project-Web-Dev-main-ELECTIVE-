<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Validator;

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
            
            // Get the best available avatar URL with cache busting
            $avatarUrl = $googleUser->avatar_original ?? $googleUser->getAvatar();
            // Add cache busting parameter
            $avatarUrl = $this->addCacheBustingToUrl($avatarUrl);
            
            // Debug the Google user data
            \Log::info('Google user data:', [
                'id' => $googleUser->id,
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'avatar' => $avatarUrl,
            ]);
            
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
            
            // Clear application caches
            $this->clearApplicationCaches();
            
            return redirect('/')->with('login_success', 'Successfully logged in with Google!');
            
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

    /**
     * Add cache busting parameter to URL
     */
    private function addCacheBustingToUrl($url)
    {
        $separator = (parse_url($url, PHP_URL_QUERY) == null) ? '?' : '&';
        return $url . $separator . 'cb=' . time();
    }

    /**
     * Clear application caches
     */
    private function clearApplicationCaches()
    {
        try {
            \Artisan::call('cache:clear');
            \Artisan::call('view:clear');
        } catch (\Exception $e) {
            \Log::error('Failed to clear caches: ' . $e->getMessage());
        }
    }
}