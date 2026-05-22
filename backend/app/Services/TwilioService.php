<?php
namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class TwilioService
{
    private ?Client $client = null;

    public function __construct(
        private readonly string $sid,
        private readonly string $token,
        private readonly string $from,
    ) {}

    private function client(): Client
    {
        return $this->client ??= new Client($this->sid, $this->token);
    }

    public function sendSms(string $to, string $message): void
    {
        if (! $this->sid || ! $this->token) {
            Log::warning('TwilioService: credentials not configured, SMS skipped.', ['to' => $to]);
            return;
        }

        try {
            $this->client()->messages->create($to, [
                'from' => $this->from,
                'body' => $message,
            ]);
        } catch (\Exception $e) {
            Log::error('Twilio SMS error: ' . $e->getMessage(), ['to' => $to]);
        }
    }
}
