<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;

use GuzzleHttp\Client;
use function Symfony\Component\Clock\now;

class PasskitService
{
    protected $client;
    protected $campaignId;

    public function __construct()
    {
        $this->campaignId = env('PASSKIT_PROGRAM_ID');

        $this->client = new Client([
            'base_uri' => env('PASSKIT_API_URL'),
            'headers' => [
                'Authorization' => 'Bearer ' . env('PASSKIT_API_KEY'),
                'Content-Type' => 'application/json'
            ]
        ]);
    }

    /**
     * Enroll a new member via REST API
     */
    public function enrollMember(string $email, string $first_name, string $last_name, string $name, string $phone, string $user_id): string
    {
        //dd($email, $first_name, $last_name, $name, $phone, $user_id);
        try {
            // WARNING: This endpoint does not exist in PassKit REST API
            $response = $this->client->post("https://api.pub1.passkit.io/members/member", [
                'json' => [
                    'programId' => $this->campaignId,
                    'tierId' => 'gift_card',
                    'person' => [
                        'forename' => $first_name,
                        'surname' => $last_name,
                        'displayName'=> $name,
                        'emailAddress' => $email,
                        'mobileNumber' => $phone,
                        'status' => 'ENROLLED',
                        'expiryDate' => Carbon::now()->addDays(365)->timestamp * 1000,
                    ],
                    'metadata' =>[
                        'userId' => $user_id ?? 1,
                    ]
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            if (!isset($data['id'])) {
                throw new Exception("PassKit API returned invalid response.");
            }

            return "https://pub1.pskt.io/" . $data['id'];

        } catch (\Exception $e) {
            throw new Exception("PassKit REST Error: " . $e->getMessage());
        }
    }

}
