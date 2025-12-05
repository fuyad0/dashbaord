<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CouponMail extends Mailable
{
    use Queueable, SerializesModels;
    public $codes;

    /**
     * Create a new message instance.
     */
    public function __construct($codes)
    {
        $this->codes = $codes;
    }

    public function build()
    {
        return $this->subject('Your Coupon Code')
                    ->view('backend.layouts.mail.couponCode')
                    ->with([
                        'codes' => $this->codes
                    ]);;
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
