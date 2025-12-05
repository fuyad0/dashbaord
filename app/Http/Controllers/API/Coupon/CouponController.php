<?php

namespace App\Http\Controllers\API\Coupon;

use Carbon\Carbon;
use Stripe\Stripe;
use App\Models\Plan;
use App\Models\User;
use App\Models\Coupon;
use App\Mail\CouponMail;
use App\Models\CouponUsage;
use App\Traits\ApiResponse;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use App\Services\PasskitService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CouponController extends Controller
{
    use ApiResponse;

    protected $passKit;

    public function __construct(PasskitService $passKit)
    {
        $this->passKit = $passKit;
    }

    /**
     * Show coupon details (DO NOT auto-apply)
     */
    public function show($code)
    {
        $coupon = Coupon::where("code", $code)->first();

        if (! $coupon) {
            return $this->success('No Data found', [], 200);
        }

        if ($coupon->is_active && $coupon->expires_at >= now()) {
            return $this->success('Coupon valid', $coupon, 200);
        }

        return $this->error('Coupon expired or inactive', 400);
    }


    /**
     * Apply coupon manually
     */
    public function applyCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'store_id' => 'nullable|numeric',
            'total_amount' => 'nullable|numeric',
        ]);

        $coupon = Coupon::where('code', $request->code)->first();

        if (!$coupon || !$coupon->is_active || now()->gt($coupon->expires_at)) {
            return $this->error('Coupon invalid or expired', 400);
        }

        CouponUsage::create([
            'user_id' => Auth::id(),
            'coupon_id' => $coupon->id,
            'store_id' => $request->store_id,
            'discount_amount' => $request->total_amount ?? 0,
        ]);

        $coupon->increment('used_count');

        return $this->success('Coupon applied successfully', $coupon, 200);
    }


    /**
     * Stripe Checkout
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'plan_id' => 'required',
        ]);

        try {

            $plan = Plan::find($request->plan_id);
            if (!$plan) {
                return $this->error('Plan not found.', 404);
            }

            $user = Auth::check() ? Auth::user() : User::find($request->user_id);
            if (! $user) {
                return $this->error('Invalid user', 404);
            }

            Stripe::setApiKey(env('STRIPE_SECRET'));

            $session = Session::create([
                'line_items' => [[
                    'price' => $plan->stripe_price_id,
                    'quantity' => $request->quantity,
                ]],
                'mode' => 'subscription',
                'customer_email' => $user->email,
                'success_url' => env('FRONTEND_URL') . '/checkout/coupon-success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'  => env('FRONTEND_URL') . '/checkout/coupon-cancel',
                'metadata' => [
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                ],
            ]);

            $end = match (strtolower($plan->name)) {
                'monthly' => now()->addMonth(),
                'yearly'  => now()->addYear(),
                default   => null,
            };

            Subscription::create([
                'user_id'       => $user->id,
                'plan_id'       => $plan->id,
                'type'          => $plan->type,
                'stripe_status' => 'pending',
                'stripe_id'     => $session->id,
                'expires_at'    => $end,
                'quantity'      => $request->quantity,
            ]);

            return response()->json([
                'checkout_url' => $session->url,
                'plan'         => $plan->title,
            ]);

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }


    /**
     * Checkout Success - Generate Coupons
     */
    public function checkoutSuccess(Request $request)
    {
        try {
            $request->validate(['session_id' => 'required']);

            Stripe::setApiKey(env('STRIPE_SECRET'));
            $session = Session::retrieve($request->session_id);

            $subscription = Subscription::where('stripe_id', $request->session_id)->first();
            $stripeSubscription = \Stripe\Subscription::retrieve($session->subscription);
            $stripePriceId = $stripeSubscription->items->data[0]->price->id ?? null;

            if (! $subscription) {
                return $this->error('Subscription not found.', 404);
            }

            if ($subscription->processed) {
                return $this->success('Already processed.', [
                    'coupons' => $subscription->generated_codes,
                    'quantity' => $subscription->quantity
                ]);
            }

            $subscription->update([
                'stripe_id' => $stripeSubscription->id,
                'stripe_status' => $stripeSubscription->status,
                'stripe_price' => $session->metadata->price_id ?? null,
                'processed' => 1,
                'type'          => $stripeSubscription->items->data[0]->plan->product ?? null,
                'stripe_status' => $stripeSubscription->status,
                'stripe_price'  => $stripePriceId,
                'quantity'      => $stripeSubscription->items->data[0]->quantity ?? 1,
                'ends_at'       => Carbon::createFromTimestamp($stripeSubscription->current_period_end) ?? now()->addYear(),
            ]);

            $subscription->user->update([
                'plan_id' => $subscription->plan_id,
                'trial_ends_at' => $subscription->ends_at,
            ]);

            $codes = $this->enroll(new Request([
                'quantity'   => $subscription->quantity,
                'first_name' => $subscription->user->first_name,
                'last_name'  => $subscription->user->last_name,
                'name'       => $subscription->user->name,
                'email'      => $subscription->user->email,
                'phone'      => $subscription->user->number,
                'dob'        => $subscription->user->dob,
                'user_id'    => $subscription->user->id,
            ]));

            return $this->success('Membership Card generated successfully!', [
                'coupons'  => $codes,
                'quantity' => $subscription->quantity,
            ]);

        } catch (\Exception $e) {
            return $this->error('Payment verification failed: ' . $e->getMessage(), 500);
        }
    }


    /**
     * Cancel coupon subscription
     */
    public function checkoutCancel(Request $request)
    {
        $user = Auth::user();

        $subscription = $user->subscriptions()
            ->whereIn('stripe_status', ['active', 'trialing'])
            ->latest()
            ->first();

        if (! $subscription) {
            return redirect()->route('payment.index')->with('t-error', 'No active subscription found.');
        }

        try {
            $subscription->cancelNow(); // cancel immediately on Stripe
            $subscription->update([
                'stripe_status' => 'cancelled',
                'ends_at'       => now(),
            ]);

            return redirect()->route('payment.index')->with('t-success', 'Subscription cancelled successfully.');
        } catch (\Exception $e) {
            return redirect()->route('payment.index')->with('t-error', 'Error: ' . $e->getMessage());
        }
    }



    /**
     * Show membership details
     */
    public function showMembership()
    {
        $user = Auth::user();

        $membership = $user->subscribed()
            ->where(function ($q) {
                $q->where('stripe_status', 'active')
                  ->whereNull('trial_ends_at')
                  ->where('ends_at', '>', now());
            })
            ->orWhere(function ($q) {
                $q->where('stripe_status', 'trialing')
                  ->where('trial_ends_at', '>', now());
            })
            ->with('plan')
            ->latest()
            ->first();

        if (! $membership) {
            return $this->error('No active membership found.', 403);
        }

        $report = [
            'first_name' => $user->first_name,
            'last_name'  => $user->last_name,
            'plan_price' => $membership->plan->price ?? null,
            'subscription_type' => $membership->stripe_status,
            'ends_at' => $membership->ends_at,
        ];

        $qr = 'https://quickchart.io/qr?text=' . urlencode('https://grandsave.se/' . json_encode($report));

        return $this->success('Membership found', [
            'data' => $report,
            'qr'   => $qr
        ]);
    }


    /**
     * Generate PassKit Coupons
     */
    public function enroll(Request $request)
    {
        $request->validate([
            'email'      => 'required|email',
            'first_name' => 'required|string',
            'last_name'  => 'required|string',
            'name'       => 'nullable|string',
            'phone'      => 'required',
            'user_id'    => 'required',
            'quantity'   => 'required|integer|min:1',
        ]);

        $codes = [];

        try {
            for ($i = 1; $i <= $request->quantity; $i++) {

                $passUrl = $this->passKit->enrollMember(
                    $request->email,
                    $request->first_name,
                    $request->last_name,
                    $request->name ?? "{$request->first_name} {$request->last_name}",
                    $request->phone,
                    $request->user_id
                );

                $codes[] = $passUrl;

                DB::table('coupons')->insert([
                    'name'       => $request->name ?? "{$request->first_name} {$request->last_name}",
                    'email'      => $request->email,
                    'code'       => $passUrl,
                    'expires_at' => now()->addYear(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $this->sendEmail([
                'email' => $request->email,
                'codes' => $codes
            ]);

            return $codes;

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }


    public function sendEmail(array $data)
    {
        $codes = collect($data['codes'])->map(fn($code) => ['original' => $code])->toArray();
        Mail::to($data['email'])->send(new CouponMail($codes));
    }
}
