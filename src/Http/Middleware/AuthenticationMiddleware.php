<?php

namespace Tustin\PlayStation\Http\Middleware;

use GuzzleHttp\Psr7\Request;
use Tustin\PlayStation\Client;

final class AuthenticationMiddleware
{
    public function __construct(public Client $client) {}

    /**
     * Intercepts all requests and inject an Authorization header with the current access token if it exists.
     */
    public function __invoke(Request $request, array $options = []): Request
    {
        $accessToken = $this->client->getAccessToken();

        if (!$accessToken) {
            return $request;
        }

        return $request->withHeader('Authorization', 'Bearer ' . $accessToken->getToken());
    }
}
