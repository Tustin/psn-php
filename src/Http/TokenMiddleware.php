<?php

namespace PlayStation\Http;

use GuzzleHttp\Psr7\Request;

final class TokenMiddleware {

    private $accessToken;

    public function __construct(string $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    public function __invoke(Request $request, array $options = [])
    {
        return $request->withHeader(
            'Authorization', sprintf('Bearer %s', $this->accessToken)
        );
    }
}