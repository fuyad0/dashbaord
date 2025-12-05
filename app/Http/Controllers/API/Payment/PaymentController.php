<?php

namespace App\Http\Controllers\API\Payment;

use Carbon\Carbon;
use Stripe\Stripe;
use App\Models\Plan;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Support\Str;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Attributes\Scope;

class PaymentController extends Controller
{
    use ApiResponse;

    public function checkout(Request $request, $id)
    {
        try {
            $plan = Plan::find($id);

            if (!$plan) {
                return response()->json(['error' => 'Plan not found.'], 404);
            }

            // Identify user safely
            if ($request->has('user_id')) {
                $user = User::find($request->get('user_id'));
            } elseif (Auth::check()) {
                $user = Auth::user();
            } else {
                return response()->json(['error' => 'No user identified. Please login.'], 401);
            }

            if (!$user) {
                return response()->json(['error' => 'Invalid user.'], 404);
            }

            $transactionId = (string) Str::uuid();

          /*  if (strtolower($plan->name) === 'free') {

                $activeFree = Subscription::where('user_id', $user->id)
                    ->where('plan_id', $plan->id)
                    ->where(function ($q) {
                        $q->whereNull('trial_ends_at')->orWhere('trial_ends_at', '>', now());
                    })
                    ->first();

                if ($activeFree) {
                    return response()->json([
                        'message' => 'You already have an active free plan.',
                    ], 400);
                }

                // ✅ Set duration (you can adjust as needed)
                $start = now();
                $end = now()->addDays(60);

                Subscription::create([
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'status' => 'active',
                    'trial_ends_at' => $end,
                ]);

                return response()->json([
                    'message' => 'Free plan activated successfully.',
                    'end_date' => $end,
                ]);
            }*/

            $activePayment = Subscription::where('users_id', $user->id)
                ->where('plan_id', $plan->id)
                ->where(function ($q) {
                    $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
                })
                ->first();

            if ($activePayment) {
                return response()->json([
                    'message' => 'You already have an active subscription for this plan.'
                ], 400);
            }

            // ✅ Initialize Stripe
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $successUrl = env('APP_URL') . '/checkout/payment-success';
            $cancelUrl = env('APP_URL') . '/checkout/payment-cancel?transaction_id=' . $transactionId;

            $session = \Stripe\Checkout\Session::create([
                'line_items' => [[
                    'price' => $plan->stripe_price_id,
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => $successUrl . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $cancelUrl,
                'subscription_data' => [
                    'trial_end' => strtotime('+60 days'),
                    'metadata' => [
                        'user_id' => $user->id,
                        'plan_id' => $plan->id,
                        'transaction_id' => $transactionId,
                    ],
                ],
            ]);

            switch (strtolower($plan->name)) {
                case 'monthly':
                    $end = now()->addMonth();
                    break;
                case 'yearly':
                    $end = now()->addYear();
                    break;
                default:
                    $end = null;
            }

            // 💾 Save payment record as pending
            Subscription::create([
                'users_id' => $user->id,
                'plan_id' => $plan->id,
                'type' => $plan->type,
                'stripe_status' => 'Pending',
                'stripe_id' => $session->id,
                'ends_at' => $end,
            ]);

            return response()->json([
                'checkout_url' => $session->url,
                'plan' => $plan->name,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function checkoutSuccess(Request $request)
    {
        try {
            $request->validate([
                'session_id' => 'required|string',
            ]);

            Stripe::setApiKey(env('STRIPE_SECRET'));

            // 1. Retrieve session details from Stripe
            $session = Session::retrieve($request->session_id);

            // 2. Find the subscription record (you may store the session_id temporarily when creating checkout)
            $subscription = Subscription::where('stripe_id', $request->session_id)->first();

            if (! $subscription) {
                return redirect()->route('payment.index')->with('t-error', 'Subscription not found for this session.');
            }

            // 3. Update subscription data after successful payment
            $subscription->update([
                'stripe_status' => 'active',
                'stripe_price' => $session->metadata->price_id ?? null, // optional
                'ends_at' => Carbon::now()->addMonth(), // or addYear() based on plan
            ]);

            // 4. Optionally, update user role/status
            $subscription->user->update([
                'plan_id' => $subscription->plan_id,
            ]);

            // 5. Return success response or redirect
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Subscription activated successfully',
                    'plan' => $subscription->plan->name ?? '',
                    'status' => 'active',
                ]);
            }

            return redirect()
                ->route('payment.index')
                ->with('t-success', 'Subscription activated successfully for plan: ' . ($subscription->plan->name ?? ''));
        } catch (\Exception $e) {
            return redirect()->route('payment.index')->with('t-error', 'Payment verification failed: ' . $e->getMessage());
        }
    }
    // 🔹 Cancel API
    public function checkoutCancel(Request $request)
    {
        try {
            $request->validate([
                'session_id' => 'required|string',
            ]);

            // Find subscription record using Stripe checkout session ID
            $subscription = Subscription::where('stripe_id', $request->session_id)->first();

            if ($subscription) {
                $subscription->update([
                    'stripe_status' => 'cancelled',
                    'ends_at' => now(),
                ]);
            }

            return response()->json([
                'message' => 'Subscription cancelled successfully.',
                'session_id' => $subscription?->stripe_id,
                'plan' => $subscription?->plan->name ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to cancel subscription: ' . $e->getMessage(),
            ], 500);
        }
    }

    // Check free plan expiry for dashboard / protected routes
    public function checkPlanExpiry()
    {
        $user = Auth::user();
        $latestPayment = Subscription::where('user_id', $user->id)
            ->latest()
            ->first();

        if (!$latestPayment) {
            return response()->json(['message' => 'No active plan'], 403);
        }

        if ($latestPayment->plan->type === 'Free' && now()->gt($latestPayment->trial_ends_at)) {
            return response()->json([
                'message' => 'Your free plan has expired. Please subscribe to continue.'
            ], 403);
        }elseif(now()->gt($latestPayment->ends_at)){
            return response()->json([
                'message' => 'Your plan has expired. Please subscribe again to continue.'
            ], 403);
        }

        return response()->json([
            'message'   => 'Your plan is currently Active',
            'plan_name' => $latestPayment->plan->title,
            'plan_type' => $latestPayment->plan->type,
            'status'    => $latestPayment->stripe_status,
            'end_date'  => $latestPayment->ends_at ? Carbon::parse($latestPayment->ends_at)->format('d-m-Y') : Carbon::parse($latestPayment->trial_ends_at)->format('d-m-Y'),
        ]);
    }
}