<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPlanExpiry
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'You must login first.'
            ], 401);
        }

        if ($user->role === 'User') {

            $check = $user->subscribed()
                ->where(function ($q) {
                    $q->where('stripe_status', 'active')
                        ->whereNull('trial_ends_at')
                        ->where('ends_at', '>', now());
                })
                ->orWhere(function ($q) {
                    $q->where('stripe_status', 'trialing')
                        ->where('trial_ends_at', '>', now());
                })
                ->select('id', 'user_id', 'plan_id', 'stripe_status', 'ends_at', 'trial_ends_at')
                ->latest()
                ->first();

            if (!$check) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Your subscription is expired.'
                ], 403);
            }
        }

        return $next($request);
    }
}
