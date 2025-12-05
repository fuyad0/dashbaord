<?php

namespace App\Http\Controllers\API\Auth;


use Carbon\Carbon;
use App\Models\User;
use Ichtrojan\Otp\Otp;
use App\Helpers\Helper;
use App\Traits\ApiResponse;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use DrewM\MailChimp\MailChimp;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Web\NewsLetter\NewsletterController;


class RegisterController extends Controller
{
    use ApiResponse;

    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'number'     => 'required|string|max:255',
            'address'    => 'nullable|string',
            'dob'        => 'nullable|date',
            'password'   => 'required|min:6',
            'is_agree'   => 'boolean',
            'role'       => 'required|in:User,Company'
        ]);

        DB::beginTransaction();

        try {

            // Create user
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
                'email'      => $request->email,
                'number'     => $request->number,
                'address'    => $request->address,
                'dob'        => $request->dob,
                'password'   => Hash::make($request->password),
                'is_agree'   => $request->is_agree,
                'role'       => $request->role,
            ]);

            // Send OTP
            $otp = $this->send_otp($user);
            if (!$otp) {
                throw new \Exception("Failed to send OTP.");
            }


            /**
             * MAILCHIMP — Always process but NEVER break registration
             * Even if already subscribed, user registration should complete.
             */
            $MailChimp = new MailChimp(env('MAILCHIMP_API_KEY'));
            $listId = env('MAILCHIMP_LIST_ID');
            $subscriberHash = md5(strtolower($user->email));

            // Check existing subscription
            $member = $MailChimp->get("lists/$listId/members/$subscriberHash");

            // If not subscribed, then subscribe
            if (!(isset($member['status']) && $member['status'] === 'subscribed')) {

                $MailChimp->put("lists/$listId/members/$subscriberHash", [
                    'email_address' => $user->email,
                    'status'        => 'subscribed',
                    'merge_fields'  => [
                        'FNAME' => $user->first_name,
                        'LNAME' => $user->last_name,
                        'PHONE' => $user->number,
                    ],
                ]);

                // Even if Mailchimp fails → DO NOT throw error
                // Registration must not fail over Mailchimp
            }


            DB::commit();

            return $this->success(
                'Registered successfully.',
                [
                    'email' => $user->email,
                    'subscription_status' => isset($member['status']) ? $member['status'] : 'subscribed',
                ],
                201
            );
        } catch (\Exception $exception) {

            DB::rollBack();
            return $this->error($exception->getMessage(), 500);
        }
    }


    public function send_otp(User $user, $mailType = 'verify')
    {
        $otp  = (new Otp)->generate($user->email, 'numeric', 6, 60);
        $message = $mailType === 'verify' ? 'Verify Your Email Address' : 'Reset Your Password';
        Mail::to($user->email)->send(new \App\Mail\OTP($otp->token, $user, $message, $mailType));
        return $otp;
    }

    public function resend_otp(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
        ]);

        try {
            $user = User::where('email', $request->email)->first();
            if ($user) {
                $otp = $this->send_otp($user);
                return $this->success('OTP send successfully.', [], 201);
            } else {
                return $this->error('Email not found', 404);
            }
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage(), 500);
        }
    }

    public function verify_otp(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'otp' => 'required|string|digits:6',
        ]);
        try {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return $this->error('Email not found', 404);
            }

            if ($user->email_verified_at !== null) {
                return $this->error('Email already verified', 404);
            }

            $verify = (new Otp)->validate($request->email, $request->otp);
            if ($verify->status) {
                $user->email_verified_at = now();
                $user->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Email verified successfully',
                    'token_type' => 'Bearer',
                    'token' => $user->createToken('AuthToken')->plainTextToken,
                    'data' => $user
                ]);
            } else {
                return $this->error($verify->message, 404);
            }
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage(), 500);
        }
    }

    public function forgot_password(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        try {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return $this->error('Email not found', 404);
            }
            $otp = $this->send_otp($user, 'forget');
            return $this->success('OTP send successfully.', [], 201);
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage(), 500);
        }
    }

    public function forgot_verify_otp(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'otp' => 'required|string|digits:6',
        ]);

        $verify = (new Otp)->validate($request->email, $request->otp);
        if ($verify->status) {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return Helper::jsonErrorResponse('Email not found', 404);
            }
            $user->reset_code = Str::random(40);
            $user->reset_code_expires_at = Carbon::now()->addDays(1);
            $user->save();
            return $this->success('OTP verified successfully', [
                'token' => $user->reset_code,
            ], 201);
        } else {
            return $this->error($verify->message, 404);
        }
    }

    public function reset_password(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'password' => 'required|string|confirmed',
        ]);

        try {
            $user = User::where('reset_code', $request->token)->first();

            if (!$user) {
                return $this->error('Invalid Token', 404);
            }

            if ($user->reset_code_expires_at < Carbon::now()) {
                return $this->error('Token expired', 404);
            }

            $user->password = Hash::make($request->password);
            $user->reset_code = null;
            $user->reset_code_expires_at = null;
            $user->save();

            return $this->ok('Password reset successfully');
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage(), 404);
        }
    }
}
