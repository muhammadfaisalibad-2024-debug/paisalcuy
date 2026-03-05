<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    /**
     * Redirect to Google OAuth page
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Find or create user
            $user = User::where('id_google', $googleUser->id)
                ->orWhere('email', $googleUser->email)
                ->first();

            if ($user) {
                // Update Google ID if not set
                if (!$user->id_google) {
                    $user->id_google = $googleUser->id;
                    $user->save();
                }
            } else {
                // Create new user
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'id_google' => $googleUser->id,
                    'password' => bcrypt(Str::random(16)), // Random password for Google users
                ]);
            }

            // Store user ID in session for OTP verification
            session(['otp_user_id' => $user->id]);

            // Redirect to OTP page
            return redirect()->route('otp.show');

        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Login Google gagal: ' . $e->getMessage());
        }
    }
}
