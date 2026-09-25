<?php

namespace App\Http\Controllers;

use App\Models\EmailVerificationOtp;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $this->checkRateLimiter($request);

        $user = User::where('email', '=', Str::lower(trim($request->email)), 'and')->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            if ($user) {
                $failKey = 'failed_logins:' . $user->id;
                $failedCount = Cache::get($failKey, 0) + 1;
                Cache::put($failKey, $failedCount, now()->addDay());

                if ($failedCount >= 3) {
                    Cache::forget($failKey);
                    $sent = $this->generateAndSendOtp($user);
                    if ($sent) {
                        return redirect()->route('verification.notice', ['email' => $user->email])
                            ->with('verify_email', $user->email)
                            ->with('status', 'Too many failed attempts. A one-time verification code has been sent to your email.');
                    }
                }
            }
            RateLimiter::hit($this->throttleKey($request), 60);
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey($request));

        if ($user->status === 'suspended' || $user->suspended_until) {
            if ($user->suspended_until && $user->suspended_until->isPast()) {
                $user->update(['status' => 'active', 'suspended_until' => null]);
            } else {
                return back()->withErrors([
                    'email' => 'Your account is temporarily suspended. Contact support for assistance.',
                ]);
            }
        }

        if ($user->status === 'inactive') {
            return back()->withErrors([
                'email' => 'Your account is inactive. Contact support for assistance.',
            ]);
        }

        $failKey = 'failed_logins:' . $user->id;
        Cache::forget($failKey);

        if (!$user->hasVerifiedEmail()) {
            $sent = $this->generateAndSendOtp($user);
            $statusMessage = $sent
                ? 'A verification code has been sent to your email.'
                : 'Unable to send verification email at this time. Please try again later or use the resend option.';
            return redirect()->route('verification.notice', ['email' => $user->email])
                ->with('verify_email', $user->email)
                ->with('status', $statusMessage);
        }

        Auth::login($user);
        $request->session()->regenerate();
        return redirect()->intended(route('dashboard'));
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:50|unique:users|alpha_dash',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', PasswordRule::min(8)->mixedCase()->numbers()->symbols()],
            'terms' => 'accepted',
        ]);

        $org = Organization::query()->first();
        $settings = $org?->settings ?? [];

        $user = User::create([
            'name' => trim(strip_tags($request->name)),
            'username' => $request->username ? trim(strip_tags($request->username)) : null,
            'email' => Str::lower(trim($request->email)),
            'password' => Hash::make($request->password),
            'status' => 'active',
            'accepted_terms_version' => $settings['terms_version'] ?? null,
            'accepted_privacy_version' => $settings['privacy_version'] ?? null,
        ]);

        if ($org) {
            $org->users()->attach($user->id, ['role' => 'customer', 'status' => 'active']);
        }

        $sent = $this->generateAndSendOtp($user);

        $statusMessage = $sent
            ? 'Account created! A verification code has been sent to your email.'
            : 'Account created! However, we were unable to send the verification email. Please use the resend option on the next page.';

        return redirect()->route('verification.notice', ['email' => $user->email])
            ->with('verify_email', $user->email)
            ->with('status', $statusMessage);
    }

    public function showVerifyForm(Request $request)
    {
        $email = $request->query('email') ?? session('verify_email');

        if (!$email && Auth::check()) {
            $email = Auth::user()->email;
        }

        if (Auth::check()) {
            /** @var \App\Models\User $authUser */
            $authUser = Auth::user();
            if ($authUser->hasVerifiedEmail()) {
                $hasOtp = EmailVerificationOtp::where('user_id', '=', Auth::id(), 'and')
                    ->whereNull('used_at')
                    ->where('expires_at', '>', now())
                    ->exists();
                if (!$hasOtp) {
                    return redirect()->route('dashboard');
                }
            }
        }

        if (!$email) {
            return redirect()->route('login');
        }

        return view('auth.verify-otp', compact('email'));
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'nullable|string|size:6',
            'digits' => 'nullable|array|size:6',
        ]);

        $otp = $request->otp ?? implode('', $request->digits ?? []);

        if (strlen($otp) !== 6) {
            return back()->withErrors(['otp' => 'Please enter a valid 6-digit verification code.']);
        }

        $user = User::where('email', '=', Str::lower(trim($request->email)), 'and')->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        $otpRecord = EmailVerificationOtp::where('user_id', '=', $user->id, 'and')
            ->where('otp', $otp)
            ->latest()
            ->first();

        if (!$otpRecord || !$otpRecord->isValid()) {
            EmailVerificationOtp::where('user_id', '=', $user->id, 'and')->delete();
            $user->update(['status' => 'suspended', 'suspended_until' => now()->addHours(24)]);
            return redirect()->route('verification.notice', ['email' => $user->email, 'support' => '1'])
                ->withErrors(['otp' => 'Invalid verification code. Submit a request below for account review.']);
        }

        Cache::forget('failed_logins:' . $user->id);

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        $otpRecord->update(['used_at' => now()]);

        EmailVerificationOtp::where('user_id', '=', $user->id, 'and')
            ->whereNull('used_at')
            ->delete();

        if (!Auth::check()) {
            Auth::login($user);
            $request->session()->regenerate();
        }

        $request->session()->forget('verify_email');

        return redirect()->route('dashboard')->with('success', 'Verification successful!');
    }

    public function resendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', '=', Str::lower(trim($request->email)), 'and')->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        $hasOtp = EmailVerificationOtp::where('user_id', '=', $user->id, 'and')
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->exists();

        if ($user->hasVerifiedEmail() && !$hasOtp) {
            return redirect()->route('dashboard');
        }

        if ($this->otpLimitExceeded($user)) {
            return redirect()->route('verification.notice', ['email' => $user->email, 'support' => '1']);
        }

        $lastOtp = EmailVerificationOtp::where('user_id', '=', $user->id, 'and')
            ->latest()
            ->first();

        if ($lastOtp && $lastOtp->created_at->diffInSeconds(now()) < 180) {
            $remaining = 180 - $lastOtp->created_at->diffInSeconds(now());
            return back()->withErrors([
                'otp' => 'Please wait ' . ceil($remaining / 60) . 'm ' . ($remaining % 60) . 's before requesting a new code.',
            ]);
        }

        $hourKey = 'otp_requests:' . $user->id . ':hour';
        $dayKey = 'otp_requests:' . $user->id . ':day';
        $hourCount = Cache::get($hourKey, 0);
        $dayCount = Cache::get($dayKey, 0);

        if ($hourCount >= 4) {
            return redirect()->route('verification.notice', ['email' => $user->email, 'support' => '1']);
        }
        if ($dayCount >= 6) {
            return redirect()->route('verification.notice', ['email' => $user->email, 'support' => '1']);
        }

        Cache::put($hourKey, $hourCount + 1, now()->addHour());
        Cache::put($dayKey, $dayCount + 1, now()->addDay());

        $sent = $this->generateAndSendOtp($user);

        $statusMessage = $sent
            ? 'A new verification code has been sent to your email.'
            : 'Unable to send verification email at this time. Please try again later.';

        return back()->with('status', $statusMessage);
    }

    protected function otpLimitExceeded(User $user): bool
    {
        $dayKey = 'otp_requests:' . $user->id . ':day';
        return Cache::get($dayKey, 0) >= 6;
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            \Illuminate\Support\Facades\DB::table('laravel_sessions')
                ->where('user_id', '=', $user->id, 'and')
                ->delete();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm(string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => request('email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', PasswordRule::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    protected function generateAndSendOtp(User $user): bool
    {
        EmailVerificationOtp::where('user_id', '=', $user->id, 'and')
            ->whereNull('used_at')
            ->delete();

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        EmailVerificationOtp::create([
            'user_id' => $user->id,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(10),
        ]);

        try {
            $user->notify(new \App\Notifications\EmailOtpNotification($otp));
            return true;
        } catch (\Symfony\Component\Mailer\Exception\TransportExceptionInterface $e) {
            EmailVerificationOtp::where('user_id', '=', $user->id, 'and')
                ->where('otp', '=', $otp)
                ->delete();
            \Illuminate\Support\Facades\Log::error('Failed to send OTP email: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
            return false;
        }
    }

    protected function checkRateLimiter(Request $request): void
    {
        $key = $this->throttleKey($request);

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => __('auth.throttle', ['seconds' => $seconds, 'minutes' => ceil($seconds / 60)]),
            ]);
        }
    }

    protected function throttleKey(Request $request): string
    {
        return Str::lower($request->input('email')) . '|' . $request->ip();
    }
}
