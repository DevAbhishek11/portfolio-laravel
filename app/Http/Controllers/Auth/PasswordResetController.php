<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordResetController extends Controller
{
    public function __construct(private OtpService $otpService) {}

    public function show(string $token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token'    => 'required|string',
            'otp'      => 'required|digits:6',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ]);

        // Find the reset record
        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email ?? '')
            ->first();

        // We embed email in the form (hidden field)
        $resetRecord = DB::table('password_reset_tokens')->get()->first(function ($r) use ($request) {
            return Hash::check($request->token, $r->token)
                && now()->diffInMinutes($r->created_at) <= config('portfolio.admin.reset_expiry_minutes', 60);
        });

        if (! $resetRecord) {
            return back()->with('error', 'Invalid or expired reset link. Please request a new one.');
        }

        $user = User::where('email', $resetRecord->email)->where('is_admin', true)->first();

        if (! $user) {
            return back()->with('error', 'User not found.');
        }

        // Verify OTP
        if (! $this->otpService->verify($user, $request->otp, 'password_reset')) {
            return back()->with('error', 'Invalid or expired OTP code.');
        }

        // Update password
        $user->update(['password' => Hash::make($request->password)]);

        // Clean up
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();

        // Invalidate all sessions (force re-login)
        session()->flush();

        return redirect()->route('admin.login')
            ->with('success', 'Password reset successfully. Please log in with your new password.');
    }
}
