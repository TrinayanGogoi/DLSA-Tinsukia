<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use App\Notifications\TwoFactorCodeNotification;

class AuthController extends Controller
{
     /**
     * Register a new admin (No Hashing)
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:admins',
            'password' => 'required|string|min:8',
        ]);

        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password, // Store plain text password
        ]);

        $token = $admin->createToken('authToken')->accessToken;

        return response()->json([
            'message' => 'Admin registered successfully',
            'token' => $token,
            'admin' => $admin
        ], 201);

    }

    /**
     * Admin login (No Hashing) generate 2fa code
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Check if admin exists with plain text password directly from database
        $admin = Admin::where('email', $request->email)
                      ->where('password', $request->password) // No Hashing
                      ->first();

        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        

        // Generate a 6-digit 2FA code
        $twoFactorCode = rand(100000, 999999);
        $admin->two_factor_code = $twoFactorCode;
        $admin->two_factor_expires_at = now()->addMinutes(10);
        $admin->save();

        // Send 2FA code via email notification
        $admin->notify(new TwoFactorCodeNotification($twoFactorCode));

        

        return response()->json([
            'message' => '2FA code sent. Please verify.',
            'email' => $admin->email,
            'two_factor_code' => $twoFactorCode // ⚠️ For development purposes only, remove this in production!
        ], 200);
    }

     /**
     * verify 2 factor authentication
     */
    public function verify2FA(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'code' => 'required|numeric',
        ]);

        $admin = Admin::where('email', $request->email)
            ->where('two_factor_code', $request->code)
            ->where('two_factor_expires_at', '>', now())
            ->first();

        if (!$admin) {
            return response()->json(['message' => 'Invalid or expired 2FA code'], 401);
        }

        // Clear the 2FA code after verification
        $admin->two_factor_code = null;
        $admin->two_factor_expires_at = null;
        $admin->save();

        // Generate auth token
        $token = $admin->createToken('authToken')->accessToken; // generates token after 2FA which is automatically hashed and stored in oauth_acces_token table

        // Store the token in the session
        session(['authToken' => $token]);

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'admin' => $admin
        ], 200);
    }

    /**
     * Logout admin (Revoke Token)
     */
    public function logout(Request $request)
    {
        $token = $request->user()->token();
        if ($token) {
            $token->delete(); // Delete the token
        }
        
        // Return response with no-cache headers
        return response()->json(['message' => 'Logged out'], 200)
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');
    }
    

    /**
     * Get authenticated admin details
     */
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }

}

