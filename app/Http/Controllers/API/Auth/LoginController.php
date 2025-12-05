<?php

namespace App\Http\Controllers\API\Auth;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LoginController extends Controller
{

    use ApiResponse;

    public function login(Request $request)
    {
        $request->merge([
            'remember_token' => filter_var($request->remember_token, FILTER_VALIDATE_BOOLEAN),
        ]);

        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return Helper::jsonErrorResponse('The provided credentials do not match our records.', 401, [
                'email' => 'The provided credentials do not match our records.'
            ]);
        }

        if (Auth::user()->email_verified_at === null) {
            return Helper::jsonErrorResponse('Email not verified.', 403, []);
        }

        $user = Auth::user();
        $user->update(['is_online' => true]);

        // handle nullable remember_token
        if ($request->remember_token) {
            $user->setRememberToken(Str::random(60));
        }

        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Login Successful',
            'token_type' => 'Bearer',
            'token' => $user->createToken('AuthToken')->plainTextToken,
            'data' => $user
        ]);
    }

    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            $user->update(['is_online' => false]);

            $request->user()->currentAccessToken()->delete();

            // Return a response indicating the user was logged out
            return $this->ok('Logged out successfully.');
        }catch (\Exception $exception){
            return $this->error($exception->getMessage(),500);
        }
    }

    public function userDetails()
    {
        $user = Auth::user()->load('userDetails');
        $membership = $user->subscribed()
        ->where(function($q){
            $q->where('stripe_status', 'active')
            ->orWhere('stripe_status', 'trialing');
        })
        ->where('quantity', 1)
        ->with('plan')
        ->latest()
        ->first();

        if(empty($membership)){
            $membership=[];
        }
        //dd($membership);
        $data=[
            'user_id' => $user->id,
            'avatar' => $user->avatar ?? '',
            'first_name' => $user->first_name ?? '',
            'last_name' => $user->last_name ?? '',
            'email' => $user->email ?? '',
            'phone' => $user->number ?? '',
            'address' => $user->address ?? '',
            'dob' => $user->dob ?? '',
            'is_agree' => $user->is_agree ?? '',
            'role' => $user->role ?? '',
            'is_online' => $user->is_online,
    
            'website'=> $user->userDetails->website ?? '',
            'whatsapp'=> $user->userDetails->whatsapp ?? '',
            'state'=> $user->userDetails->state ?? '',
            'zip'=> $user->userDetails->zip ?? '',
            'facebook'=> $user->userDetails->facebook ?? '',
            'twitter'=> $user->userDetails->twitter ?? '',
            'youtube'=> $user->userDetails->youtube ?? '',
            'tiktok'=> $user->userDetails->tiktok ?? '',

            'plan_id' => $membership->plan_id ?? '',
            'plan_name' => $membership->plan->title ?? '',
            'plan_type' => $membership->plan->type ?? '',
            'plan_status' => $membership->stripe_status ?? '',
            'ends_at' => optional($membership)->ends_at 
                ?? optional($membership)->trial_ends_at 
                ?? '',

        ];

        return $this->ok('User Details fetch successfully.', $data);
    }

}
