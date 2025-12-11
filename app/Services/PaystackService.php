<?php

namespace App\Services;

use GuzzleHttp\Client;
use Exception;

class PaystackService
{
    protected $client;
    protected $secretKey;

    public function __construct()
    {
        $this->secretKey = config('services.paystack.secret_key');

        if (!$this->secretKey) {
            throw new Exception('Paystack secret key is not configured.');
        }

        $this->client = new Client([
            'base_uri' => 'https://api.paystack.co',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type'  => 'application/json',
            ],
            'verify' => false,
        ]);
    }

    /**
     * Initialize Paystack transaction
     */
    public function initializeTransaction(array $data)
    {
        $response = $this->client->post('/transaction/initialize', [
            'json' => $data
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * Verify Paystack transaction
     */
    public function verifyTransaction(string $reference)
    {
        $response = $this->client->get("/transaction/verify/{$reference}");
        return json_decode($response->getBody(), true);
    }
}
