<?php

use GuzzleHttp\Psr7\Request;
use Tustin\PlayStation\Client;
use Tustin\PlayStation\Http\Middleware\AuthenticationMiddleware;

it('adds bearer token header when access token exists', function (): void {
    $client = new Client();
    $client->setAccessToken('abc123');

    $middleware = new AuthenticationMiddleware($client);
    $request = new Request('GET', 'https://example.com');

    $updated = $middleware($request);

    expect($updated->getHeaderLine('Authorization'))->toBe('Bearer abc123');
});

it('keeps request unchanged when no access token exists', function (): void {
    $client = new Client();

    $middleware = new AuthenticationMiddleware($client);
    $request = new Request('GET', 'https://example.com');

    $updated = $middleware($request);

    expect($updated->getHeaderLine('Authorization'))->toBe('');
});
