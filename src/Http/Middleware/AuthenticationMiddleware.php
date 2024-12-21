<?php

namespace Tustin\PlayStation\Http\Middleware;

use GuzzleHttp\Psr7\Request;

final class AuthenticationMiddleware
{
    private array $authenticationItems = [];

    public function __construct(array $authenticationItems)
    {
        $this->authenticationItems = $authenticationItems;
    }

    /**
     * Intercepts all requests to inject any authentication headers.
     */
    public function __invoke(Request $request, array $options = []): Request
    {
        foreach ($this->authenticationItems as $key => $value) {
            $request = $request->withHeader($key, $value);
        }

        return $request;
    }
}
