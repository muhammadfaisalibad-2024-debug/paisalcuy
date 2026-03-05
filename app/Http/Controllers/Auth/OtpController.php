<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OtpController extends Controller
{
    /**
     * Show OTP input form
     */
    public function show()
    {
        if (!session()->has('otp_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.otp');
    }

    /**
     * Generate and send OTP
     */
    public function generate()
    {
        $userId = session('otp_user_id');
        
        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Generate 6 digit OTP
        $otp = strtoupper(Str::random(6));
        
        // Save OTP to user
        $user->otp = $otp;
        $user->save();

        // Send OTP via email
        try {
            Mail::raw("Kode OTP Anda adalah: {$otp}\n\nKode ini berlaku untuk satu kali login.", function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Kode OTP Login - Koleksi Buku');
            });

            return response()->json([
                'success' => true,
                'message' => 'Kode OTP telah dikirim ke email Anda'
            ]);
        } catch (\Exception $e) {
            // Jika mail gagal, tetap kembalikan OTP di response (untuk development/testing)
            return response()->json([
                'success' => true,
                'message' => 'Email gagal terkirim. Kode OTP Anda: ' . $otp,
                'otp_debug' => $otp
            ]);
        }
    }

    /**
     * Verify OTP and login
     */
    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6'
        ]);

        $userId = session('otp_user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Sesi telah berakhir. Silakan login kembali.');
        }

        $user = User::find($userId);
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'User tidak ditemukan.');
        }

        // Verify OTP
        if (strtoupper($request->otp) === $user->otp) {
            // Clear OTP
            $user->otp = null;
            $user->save();

            // Clear session
            session()->forget('otp_user_id');

            // Login user
            Auth::login($user);

            return redirect()->intended('/')->with('success', 'Login berhasil!');
        }

        return back()->withErrors(['otp' => 'Kode OTP tidak valid. Silakan coba lagi.']);
    }
}
