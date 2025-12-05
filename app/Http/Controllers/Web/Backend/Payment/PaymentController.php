<?php

namespace App\Http\Controllers\Web\Payment;

use Exception;
use Carbon\Carbon;
use Stripe\Stripe;
use App\Models\Plan;
use App\Models\User;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\API\Payment\PaymentController as PaymentPaymentController;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Subscription::with(['user', 'plan'])->get();
            //dd($data);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('user', function ($data) {
                    $name  = optional($data->user)->first_name ?? 'N/A';
                    $email = optional($data->user)->email ?? 'N/A';

                    return e("$name ($email)");
                })

                ->addColumn('plan', function ($data) {
                    return e(optional($data->plan)->title ?? 'N/A');
                })
                ->addColumn('created_at', function ($d) {
                    return $d->created_at ? $d->created_at->format('Y-m-d H:i') : 'N/A';
                })
                ->addColumn('status', function ($data) {
                    $status = strtolower($data->stripe_status ?? 'pending');

                    // If cancelled → return immediately
                    if ($status === 'cancelled') {
                        return '<span class="badge bg-danger">Cancelled</span>';
                    }

                    // Check expiration ONLY for active/paid subscriptions
                    if (in_array($status, ['active', 'paid', 'success'])) {
                        if ($data->plan && $data->created_at) {
                            $start = $data->created_at;
                            $end = $data->ends_at;

                            $expectedEnd = match ($data->plan->type) {
                                'Monthly' => $start->copy()->addMonth(),
                                'Yearly' => $start->copy()->addYear(),
                                default => $end ?? $start->copy()->addMonth(),
                            };

                            if (now()->greaterThan($expectedEnd)) {
                                return '<span class="badge bg-danger">Expired</span>';
                            }
                        }

                        return '<span class="badge bg-success">Paid</span>';
                    }

                    // Pending or unknown statuses
                    return match ($status) {
                        'pending' => '<span class="badge bg-info">Pending</span>',
                        default => '<span class="badge bg-secondary">' . e($data->stripe_status) . '</span>',
                    };
                })
                ->addColumn('action', function ($data) {
                    $buttons = '<div class="btn-group btn-group-sm" role="group" aria-label="Offer Actions">';
                    $buttons .= '
                        <form action="/admin/payment/cancel/' . $data->stripe_id . '" method="POST" style="display:inline;">
                            ' . csrf_field() . '
                            <button type="submit" class="btn btn-primary text-white btn-sm" title="Cancel Subscription">
                                <i class="fe fe-x"></i>
                            </button>
                        </form>
                        ';

                    $buttons .= '<a href="' . route('payment.show', $data->id) . '" class="btn btn-info text-white" title="View"><i class="fe fe-eye"></i></a>';
                    $buttons .= '<button type="button" onclick="deleteItem(' . $data->id . ')" class="btn btn-danger text-white" title="Delete"><i class="fe fe-trash"></i></button>';
                    $buttons .= '</div>';
                    return $buttons;
                })

                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('backend.layouts.payment.index');
    }

    public function show($id)
    {
        $data = Subscription::with(['user', 'plan'])->findOrFail($id);
        return view('backend.layouts.payment.view', compact('data'));
    }

    public function checkoutCancel($id)
    {
        $subscription = Subscription::whereIn('stripe_status', ['active', 'trialing'])
            ->where('stripe_id', $id)
            ->first();

        // Check: subscription exists
        if (!$subscription) {
            return redirect()->route('payment.index')->with('t-error', 'No active subscription found for cancellation.');
        }

        // Check: already cancelled?
        if ($subscription->stripe_status === 'cancelled') {
            return redirect()->route('payment.index')->with('t-error', 'This subscription is already cancelled.');
        }

        // Check: expired? then no need to cancel in Stripe
        if ($subscription->ends_at && now()->greaterThan($subscription->ends_at)) {
            $subscription->update([
                'stripe_status' => 'expired',
            ]);

            return redirect()->route('payment.index')
                ->with('t-error', 'The subscription is already expired.');
        }

        try {
            // Cancel immediately in Stripe
            if (method_exists($subscription, 'cancelNow')) {
                $subscription->cancelNow();
            }

            // Update local DB
            $subscription->update([
                'stripe_status' => 'cancelled',
                'ends_at'       => now(),
                'trial_ends_at' => now(),
            ]);

            return redirect()->route('payment.index')
                ->with('t-success', 'Subscription cancelled successfully.');
        } catch (\Exception $e) {

            return redirect()->route('payment.index')
                ->with('t-error', 'Cancellation error: ' . $e->getMessage());
        }
    }



    public function create()
    {
        $plans = Plan::where('status', 'active')->get();
        $users = User::select('id', 'first_name', 'last_name')->get();
        return view('backend.layouts.payment.create', compact('users', 'plans'));
    }



    public function store(Request $request)
    {
        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $request->validate([
                'plan_id' => 'required|exists:plans,id',
                'user_id' => 'required',
            ]);

            $plan = Plan::find($request->plan_id);
            $user = $user = User::findOrFail($request->user_id);

            if (!$user) {
                return back()->with('t-error', 'You must be logged in.');
            }

            $activeSubscription = $user->subscribed()
                ->whereIn('stripe_status', ['active', 'trialing'])
                ->latest()
                ->first();

            if ($activeSubscription) {
                return view('payment.subscription-active', [
                    'subscription' => $activeSubscription,
                    'plan'         => $plan,
                ]);
            }

            // Build session
            $sessionData = [
                'line_items' => [[
                    'price' => $plan->stripe_price_id,
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'customer_email' => $user->email,
                'subscription_data' => [
                    'metadata' => [
                        'user_id' => $user->id,
                        'plan_id' => $plan->id,
                    ],
                ],
                'success_url' => env('APP_URL') . '/admin/success-subscription?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'  => env('APP_URL') . '/admin/cancel-subscription',

            ];

            // Give 60 days trial only for first time users
            if (!$user->subscriptions()->exists()) {
                $sessionData['subscription_data']['trial_end'] = strtotime('+60 days');
            }

            $session = Session::create($sessionData);

            return redirect()->away($session->url);
        } catch (\Exception $e) {

            return back()->with('t-error', $e->getMessage());
        }
    }

    public function checkoutSuccess(Request $request)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        $sessionId = $request->query('session_id');
        if (!$sessionId) {
            return back()->with('t-error', 'Session ID missing');
        }

        try {
            $user = Auth::user();
            if (!$user) return back()->with('t-error', 'You must be logged in.');

            // Retrieve Checkout Session
            $session = \Stripe\Checkout\Session::retrieve($sessionId);

            if (!$session || $session->payment_status !== 'paid') {
                return back()->with('t-error', 'Payment not completed');
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

            return back()->with('t-success', 'Subscription successful');
        } catch (\Exception $e) {
            Log::error("Subscription Success Error: " . $e->getMessage());
            return back()->with('t-error', $e->getMessage);
        }
    }




    public function destroy($id)
    {
        $payment = Subscription::findOrFail($id);
        $payment->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
