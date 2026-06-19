<?php

namespace App\Services\Salesforce;

use Illuminate\Support\Facades\Http;

class SalesforceClient
{
    private string $accessToken;
    private string $instanceUrl;

    public function __construct()
    {
        $this->authenticate();
    }

    private function authenticate(): void
    {
        $response = Http::asForm()->post(config('salesforce.login_url') . '/services/oauth2/token', [
            'grant_type'    => 'password',
            'client_id'     => config('salesforce.client_id'),
            'client_secret' => config('salesforce.client_secret'),
            'username'      => config('salesforce.username'),
            'password'      => config('salesforce.password') . config('salesforce.security_token'),
        ]);

        $this->accessToken = $response->json('access_token');
        $this->instanceUrl = $response->json('instance_url');
    }

    public function get(string $endpoint): array
    {
        return Http::withToken($this->accessToken)
            ->get($this->instanceUrl . '/services/data/v57.0/' . $endpoint)
            ->json();
    }

    public function post(string $endpoint, array $data): array
    {
        return Http::withToken($this->accessToken)
            ->post($this->instanceUrl . '/services/data/v57.0/' . $endpoint, $data)
            ->json();
    }
}
