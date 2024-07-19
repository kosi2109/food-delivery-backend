<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Customer;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Otp\ForgotPasswordOtp;
use App\Otp\UserRegistrationOtp;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use SadiqSalau\LaravelOtp\Facades\Otp;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers',
            'phone_number' => 'required|string|max:15|unique:customers',
            'address' => 'required|string|max:255',
            'password' => 'required|string|min:6',
        ]);

        return DB::transaction(function () use ($request) {
            $customer = Customer::where('email', $request->email)->first();

            if ($customer) {
                if ($customer->email_verified_at !== null) {
                    throw new Exception("Account already exists with this email.");
                }
            } else {
                $customer = Customer::create([
                    'fullname' => $request->fullname,
                    'email' => $request->email,
                    'phone_number' => $request->phone_number,
                    'address' => $request->address,
                    'password' => Hash::make($request->password),
                ]);

                if (!$customer) {
                    return response()->json(['message' => 'Registration failed'], 500);
                }
            }

            $otp = Otp::identifier($request->email)->send(
                new UserRegistrationOtp($customer),
                Notification::route('mail', $request->email)
            );

            if ($otp['status'] != Otp::OTP_SENT) {
                throw new Exception(__($otp['status']));
            }

            return $this->login($request);
        });
    }

    public function emailVerify(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'code' => 'required|string'
        ]);

        $otp = Otp::identifier($request->email)->attempt($request->code);

        if ($otp['status'] != Otp::OTP_PROCESSED) {
            throw new Exception(__($otp['status']));
        }

        $customer = Customer::where('email', $request->email)->firstOrFail();

        if ($customer->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified']);
        }

        $customer->markEmailAsVerified();

        return response()->json(['message' => 'Email verified successfully']);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $customer = Customer::where('email', $request->email)->first();

        if (!$customer || !Hash::check($request->password, $customer->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $customer->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'customer' => $customer
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function changePassword(Request $request)
    {
        try {
            $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:6|different:current_password',
                'password_confirmation' => 'required|same:new_password',
            ]);

            $customer = Auth::user();

            if (!$customer || !Hash::check($request->current_password, $customer->password)) {
                return response()->json([
                    'message' => 'The provided current password is incorrect.',
                ], 400);
            }

            $customer->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'message' => 'Password changed successfully',
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while changing the password',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function forgotPassword(Request $request)
    {
        try {
            $request->validate(['email' => 'required|email']);

            $customer = Customer::where('email', $request->email)->first();

            if (!$customer) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }

            $otp = Otp::identifier($request->email)->send(
                new ForgotPasswordOtp(),
                Notification::route('mail', $request->email)
            );
    
            if ($otp['status'] != Otp::OTP_SENT) {
                throw new Exception(__($otp['status']));
            }

            return response()->json([
                'message' => 'OTP sent to your email'
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while processing your request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'otp' => 'required|string',
            ]);

            if (Otp::validate($request->email, $request->otp)) {

                $customer = Customer::where('email', $request->email)->first();

                if (!$customer) {
                    return response()->json([
                        'message' => 'User not found'
                    ], 404);
                }

                $token = Str::random(64);

                DB::table('password_reset_tokens')->updateOrInsert(
                    ['email' => $customer->email],
                    [
                        'token' => Hash::make($token),
                        'created_at' => now()
                    ]
                );

                return response()->json([
                    'message' => 'OTP verified successfully',
                    'reset_token' => $token
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Invalid or expired OTP'
                ], 400);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while verifying OTP',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:customers,email',
                'password' => 'required|string|min:6',
                'password_confirmation' => 'required|same:password',
                'code' => 'required|string'
            ]);

            $otp = Otp::identifier($request->email)->attempt($request->code);

            if ($otp['status'] != Otp::OTP_PROCESSED) {
                throw new Exception(__($otp['status']));
            }

            $customer = Customer::where('email', $request->email)->first();

            if (!$customer) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            }

            $customer->password = Hash::make($request->password);

            $customer->update(); 

            if (!$customer->save()) {
                throw new Exception("Password Reset Fail.");
            }

            return response()->json(['message' => 'Password was successfully reset.']);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while resetting the password',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function resendOtp(Request $request)
    {
        $otp = Otp::identifier($request->email)->update();

        if ($otp['status'] == Otp::OTP_EMPTY) {
            $customer = Customer::where('email', $request->email)->first();

            if ($customer->email_verified_at) {
                throw new Exception("Email Already Verified.");
            }

            $otp = Otp::identifier($request->email)->send(
                new UserRegistrationOtp(
                    $customer
                ),
                Notification::route('mail', $request->email)
            );

            if ($otp['status'] != Otp::OTP_SENT) {
                throw new Exception(__($otp['status']));
            }

            return __($otp['status']);
        } elseif ($otp['status'] != Otp::OTP_SENT) {
            throw new Exception(__($otp['status']));
        }

        return response()->json([
            'status' => true,
            'message' => 'OTP Resend.',
        ], 200);
    }
}
