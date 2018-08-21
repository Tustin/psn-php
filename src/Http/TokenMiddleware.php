<?php

namespace PlayStation\Http;

use GuzzleHttp\Psr7\Request;

final class TokenMiddleware {

    private $accessToken;
    private $refreshToken;
    private $expireTime;


    public function __construct(string $accessToken, string $refreshToken, \DateTime $expireTime)
    {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->expireTime = $expireTime;
    }

    public function __invoke(Request $request, array $options = [])
    {
        return $request->withHeader(
            'Authorization', sprintf('Bearer %s', $this->accessToken)
        );
    }
}