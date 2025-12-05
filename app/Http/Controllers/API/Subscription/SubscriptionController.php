<?php

namespace App\Http\Controllers\API\Subscription;

use Carbon\Carbon;
use Stripe\Stripe;
use App\Models\Plan;
use App\Traits\ApiResponse;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class SubscriptionController extends Controller
{
    use ApiResponse;

    public function createCheckoutSession(Request $request)
    {
        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $request->validate([
                'package_id' => 'required',
            ]);

            $package = Plan::find($request->input('package_id'));

            if (!$package) {
                return $this->error('Plan not found', 404);
            }

            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'You must be logged in'], 401);
            }

            $activeSubscription = $user->subscriptions()->latest()->first();

            if ($activeSubscription) {
                $stripeStatus = $activeSubscription->stripe_status;
                $trialEndsAt = $activeSubscription->trial_ends_at ?? $activeSubscription->ends_at;

                // If user is still on trial or active subscription
                if ($stripeStatus === 'trialing' || $stripeStatus === 'active') {
                    return $this->success('Oops Subscription found Trial Period', [
                        'trial_ends_at' => $trialEndsAt->format('d-m-Y')
                    ], 200);
                } else {
                    $session = Session::create([
                        'line_items' => [[
                            'price' => $package->stripe_price_id,
                            'quantity' => 1,
                        ]],
                        'mode' => 'subscription',
                        'customer_email' => $user->email,
                        'subscription_data' => [
                            'metadata' => [
                                'user_id' => $user->id,
                            ],
                        ],
                        'success_url' => env('FRONTEND_URL') . '/success-subscription?session_id={CHECKOUT_SESSION_ID}',
                        'cancel_url'  => env('FRONTEND_URL') . '/cancel-subscription',
                    ]);
                }
            } else {
                // First-time subscription → give +60 days trial
                $session = Session::create([
                    'line_items' => [[
                        'price' => $package->stripe_price_id,
                        'quantity' => 1,
                    ]],
                    'mode' => 'subscription',
                    'customer_email' => $user->email,
                    'subscription_data' => [
                        'trial_end' => strtotime('+60 days'),
                        'metadata' => [
                            'user_id' => $user->id,
                        ],
                    ],
                    'success_url' => env('FRONTEND_URL') . '/success-subscription?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url'  => env('FRONTEND_URL') . '/cancel-subscription',
                ]);
            }

            $checkoutUrl = [
                'session_id' => $session->id,
                'url' => $session->url,
                'stripe_status' => $stripeStatus ?? NULL,
            ];

            return $this->success('Checkout session created successfully!', $checkoutUrl, 200);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function subscribeSuccess(Request $request)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $sessionId = $request->query('session_id');
        if (!$sessionId) {
            return $this->error('Session ID missing', 400);
        }

        try {
            $user = Auth::user();
            if (!$user) return $this->error('You must be logged in', 401);

            // Retrieve Checkout Session
            $session = \Stripe\Checkout\Session::retrieve($sessionId);

            if (!$session || $session->payment_status !== 'paid') {
                return $this->error('Payment not completed', 402);
            }

            // Create Stripe customer if not exists
            if (!$user->stripe_id) {
                $user->createAsStripeCustomer();
            }

            // Get Stripe subscription
            $stripeSubscription = \Stripe\Subscription::retrieve($session->subscription);
            $stripePriceId = $stripeSubscription->items->data[0]->price->id ?? null;

            // Get package
            $package = Plan::where('stripe_price_id', $stripePriceId)->first();

            // Cancel previous active subscription
            $existingSubscription = $user->subscription('default');
            if ($existingSubscription && $existingSubscription->stripe_status === 'active') {
                \Stripe\Subscription::update($existingSubscription->stripe_id, [
                    'cancel_at_period_end' => true,
                ]);
                $existingSubscription->update(['ends_at' => now()]);
            }

            // Prevent duplicate
            $subscription = $user->subscriptions()->updateOrCreate(
                ['stripe_id' => $stripeSubscription->id],
                [
                    'type'          => $stripeSubscription->items->data[0]->plan->product ?? null,
                    'stripe_status' => $stripeSubscription->status,
                    'stripe_price'  => $stripePriceId,
                    'quantity'      => $stripeSubscription->items->data[0]->quantity ?? 1,
                    'plan_id'       => $package->id ?? null,
                    'trial_ends_at' => $stripeSubscription->trial_end
                        ? Carbon::createFromTimestamp($stripeSubscription->trial_end)
                        : null,
                    'ends_at'       => $stripeSubscription->trial_end
                        ? null
                        : Carbon::createFromTimestamp($stripeSubscription->current_period_end),
                ]
            );

            $subscription->save();

            // Send email
            try {
                Mail::to($user->email)->send(new \App\Mail\SubscriptionSuccessMail($package, $subscription));
            } catch (\Exception $e) {
                Log::warning("Email send failed: " . $e->getMessage());
            }

            return $this->success('Subscription successful', [
                'plan_type'     => $package?->type,
                'plan_title'    => $package?->title,
                'stripe_status' => $subscription->stripe_status,
                'trial_ends_at' => $subscription->trial_ends_at?->format('d-m-Y'),
                'ends_at'       => $subscription->ends_at?->format('d-m-Y'),
                'quantity'      => $subscription->quantity,
                'created_at'    => $subscription->created_at->format('d-m-Y'),
            ], 200);
        } catch (\Exception $e) {
            Log::error("Subscription Success Error: " . $e->getMessage());
            return $this->error($e->getMessage(), 500);
        }
    }




    public function successSubscription()
    {
        return redirect(config('app.frontend_url') . '/payment-successful');
    }

    public function cancelSubscription()
    {
        $user = Auth::user();

        $subscription = $user->subscriptions()
            ->whereIn('stripe_status', ['active', 'trialing'])
            ->latest()
            ->first();

        if (!$subscription) {
            return $this->error('No active subscription found', 400);
        }

        if (!$subscription->stripe_id) {
            return $this->error('Stripe subscription ID is missing. Cannot cancel subscription.', 500);
        }

        try {
            // Cancel at Stripe
            $subscription->cancelNow();

            // Update DB
            $subscription->update([
                'stripe_status' => 'cancelled',
                'ends_at' => now()
            ]);

            return $this->success('Subscription cancelled successfully.', [], 200);
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage(), 500);
        }
    }


    public function getSubscriptionStatus()
    {
        $user = Auth::user();

        // Fetch the active subscription (assuming only one active subscription)
        $subscription = $user->subscribed()->where('stripe_status', 'active')->first();

        if (!$subscription) {
            return $this->error('Invalid subscription', 400);
        }

        // Return the status and end date (if available)
        return $this->success('Subscription get successfully!', $subscription, 200);
    }

    public function userSubscriptionCheck()
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'You are not authorized'], 401);
        }
        $user = Auth::user();

        $subscription = $user->subscriptions()->where('stripe_status', 'active')->first();

        if (!$subscription) {
            $createdAt = Carbon::parse($user->created_at);
            $trialEnd = $createdAt->addDays(0);
            $now = Carbon::now();

            if ($now->greaterThanOrEqualTo($trialEnd)) {
                $remainingTime = "Trial expired";
                $status = false;
            } else {
                $diff = $now->diff($trialEnd);
                $remainingTime = "{$diff->d} days, {$diff->h} hours, {$diff->i} mins";
                $status = true;
            }

            return $this->success('Trial period details', [
                'created_at' => $user->created_at,
                'trial_end' => $trialEnd,
                'remaining_time' => $remainingTime,
                'trial_status' => $status,
                'package_status' => false,
            ], 200);
        }

        $package = Plan::where('stripe_product_id', $subscription->type)->first();

        if (!$package) {
            return $this->error('Plan not found!', 400);
        }

        // Subscription start date
        $userSubscriptionStartDate = Carbon::parse($subscription->created_at);

        // Subscription end date based on package duration (assuming duration is in days)
        $subscriptionEndDate = $userSubscriptionStartDate->addDays($package->duration);

        // Calculate remaining time
        $now = Carbon::now();

        if ($now->greaterThanOrEqualTo($subscriptionEndDate)) {
            $remainingTime = "Subscription expired";
            $packageStatus = false;
        } else {
            $diff = $now->diff($subscriptionEndDate);

            // Build formatted remaining time
            $remainingTimeParts = [];
            if ($diff->y > 0) $remainingTimeParts[] = "{$diff->y} year";
            if ($diff->m > 0) $remainingTimeParts[] = "{$diff->m} months";
            if ($diff->d > 0) $remainingTimeParts[] = "{$diff->d} days";
            if ($diff->h > 0) $remainingTimeParts[] = "{$diff->h} hours";
            if ($diff->i > 0) $remainingTimeParts[] = "{$diff->i} mins";

            $remainingTime = implode(', ', $remainingTimeParts);
            $packageStatus = true;
        }

        $data = [
            'user_subscription' => $subscription,
            'package' => $package,
            'remaining_time' => $remainingTime,
            'package_status' => $packageStatus,
        ];

        return $this->success('Subscription get successfully!', $data, 200);
    }

    public function sendMail(Request $request, $id)
    {
        $user = Auth::user();

        $package = Plan::find($id);

        $system = SystemSetting::first();

        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'telephone_number' => 'nullable',
            'company_name' => 'nullable|string|max:255',
            'message' => 'nullable',
        ]);

        $data['user'] = $user;
        $data['package'] = $package;

        //        Mail::to($system->email)->send(new SubscriptionMail($data));

        return $this->success('Subscription email sent successfully!', $data, 200);
    }
}
