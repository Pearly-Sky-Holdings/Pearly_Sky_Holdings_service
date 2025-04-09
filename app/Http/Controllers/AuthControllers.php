<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\PasswordReset;
use Carbon\Carbon;


class AuthControllers extends Controller
{
    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:4|max:12'
        ]);

        $user = Customer::where('email', $fields['email'])->first();

        if (!$user) {
            $user = Employee::where('email', $fields['email'])->first();
        }

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response(['message' => 'Bad credits'], 401);
        }

        $token = $user->createToken('token')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->email;

        // Check if user exists
        $employee = Employee::where('email', $email)->first();
        $user = $employee;
        $userType = 'employee';

        if (!$employee) {
            $customer = Customer::where('email', $email)->first();
            $user = $customer;
            $userType = 'customer';

            if (!$customer) {
                return response([
                    'message' => 'User not found'
                ], 404);
            }
        }

        // Generate OTP
        $otp = sprintf("%06d", mt_rand(100000, 999999));

        // Delete any old OTPs for this email
        PasswordReset::where('email', $email)->delete();

        // Save new OTP
        PasswordReset::create([
            'email' => $email,
            'otp' => $otp,
            'user_type' => $userType,
            'created_at' => Carbon::now(),
            'expires_at' => Carbon::now()->addMinutes(15)
        ]);

        // Send email with OTP
        try {
            Mail::send('emails.reset_password', ['otp' => $otp], function ($message) use ($email) {
                $message
                    ->from('Systempearlyskycleaningplc@gmail.com', 'PearlySky PLC')
                    ->to($email)->subject('Reset Password OTP');
            });

            return response([
                'message' => 'OTP has been sent to your email'
            ], 200);
        } catch (\Exception $e) {
            return response([
                'message' => 'Error sending email',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function verifyOtpAndResetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string',
            'password' => 'required|string'
        ]);

        $email = $request->email;
        $otp = $request->otp;


        // Check if OTP exists and is valid
        $passwordReset = PasswordReset::where('email', $email)
            ->where('otp', $otp)
            ->where('expires_at', '>', Carbon::now())
            ->first();



        if (!$passwordReset) {
            return response([
                'message' => 'Invalid or expired OTP'
            ], 400);
        }


        // Update password based on user type
        if ($passwordReset->user_type === 'employee') {
            $user = Employee::where('email', $email)->first();
        } else {
            $user = Customer::where('email', $email)->first();
        }
        if (!$user) {
            return response([
                'message' => 'User not found'
            ], 404);
        }

        try {
            $user->password = Hash::make($request->password);
            $result = $user->save();

            if (!$result) {
                return response(['message' => 'Failed to update password'], 500);
            }

            // Delete the used OTP
            $passwordReset->delete();

            return response(['message' => 'Password has been reset successfully'], 200);
        } catch (\Exception $e) {
            return response(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string'
        ]);

        $email = $request->email;
        $otp = $request->otp;

        // Check if OTP exists and is valid
        $passwordReset = PasswordReset::where('email', $email)
            ->where('otp', $otp)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$passwordReset) {
            return response([
                'message' => 'Invalid or expired OTP'
            ], 400);
        }

        return response([
            'message' => 'OTP verified successfully'
        ], 200);
    }


    public function forgotPasswordWithotUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);


        $email = $request->email;
        $userType = 'customer';

        $request = DB::table('customers')
            ->Where('email', 'LIKE', '%' . $request->email . '%')
            ->get();

        if (!$request) {
            // Generate OTP
            $otp = sprintf("%06d", mt_rand(100000, 999999));

            // Save new OTP
            PasswordReset::create([
                'email' => $email,
                'otp' => $otp,
                'user_type' => $userType,
                'created_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addMinutes(15)
            ]);

            // Send email with OTP
            try {
                Mail::send('emails.reset_password', ['otp' => $otp], function ($message) use ($email) {
                    $message
                        ->from('Systempearlyskycleaningplc@gmail.com', 'PearlySky PLC')
                        ->to($email)->subject('Reset Password OTP');
                });

                return response([
                    'message' => 'OTP has been sent to your email'
                ], 200);
            } catch (\Exception $e) {
                return response([
                    'message' => 'Error sending email',
                    'error' => $e->getMessage()
                ], 500);
            }
        } else {
            return response([
                'message' => 'Email Is used',
            ], 500);
        }


    }
}