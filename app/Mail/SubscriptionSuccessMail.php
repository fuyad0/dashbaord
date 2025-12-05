<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public $plan;
    public $subscription;

    /**
     * Create a new message instance.
     */
    public function __construct($plan, $subscription)
    {
        $this->plan = $plan;
        $this->subscription = $subscription;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your Subscription is Active!')
                    ->view('backend.layouts.mail.subscription_success');
    }
}
