<?php

namespace App\Http\Controllers\API\Newsletter;

use Illuminate\Http\Request;
use DrewM\MailChimp\MailChimp;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;

class NewsletterController extends Controller
{
    use ApiResponse;

    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'number' => 'nullable|max:20',
            'address' => 'nullable'
        ]);

        $MailChimp = new MailChimp(env('MAILCHIMP_API_KEY'));
        $listId = env('MAILCHIMP_LIST_ID');
        $subscriberHash = md5(strtolower($request->email));

        $member = $MailChimp->get("lists/$listId/members/$subscriberHash");

        if ($MailChimp->success() && isset($member['status']) && $member['status'] == 'subscribed') {
            return $this->error('You are already subscribed!', 200);
        }

        if (isset($member['status']) && $member['status'] == 404) {

            $result = $MailChimp->put("lists/$listId/members/$subscriberHash", [
                'email_address' => $request->email,
                'status' => 'subscribed',
                'merge_fields' => [
                    'FNAME' => $request->first_name ?? 'first_name',
                    'LNAME' => $request->last_name ?? 'last_name',
                    'PHONE' => $request->number ?? 'number',
                    'ADDRESS' => $request->address ?? 'address',
                    'ROLE' => 'Subscriber',
                ],
            ]);

            if ($MailChimp->success()) {
                return $this->success('Subscribed successfully!', $result ,200);
            } else {
                return $this->error('Failed to subscribe. '.$MailChimp->getLastError() , 400);
            }
        }
        if (!$MailChimp->success()) {
            return $this->error('Mailchimp API error: '.$MailChimp->getLastError() , 400);
        }

        return $this->error('Unknown Error', 400);
    }
}
