<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();

        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'username' => 'nullable|string|max:50|alpha_dash|unique:users,username,' . $user->id,
                'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            ]);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }

        $emailChanged = $data['email'] !== $user->email;

        $user->name = $data['name'];
        $user->username = $data['username'];

        if ($emailChanged) {
            $user->email = $data['email'];
            $user->email_verified_at = null;
            $user->save();
            $user->sendEmailVerificationNotification();
        } else {
            $user->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile updated.' . ($emailChanged ? ' Verification email sent to your new address.' : ''),
            'verified' => $user->hasVerifiedEmail(),
        ]);
    }
}
