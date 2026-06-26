<?php

use Carbon\Carbon;
use Tustin\PlayStation\OAuthToken;

it('stores token and expiration information', function (): void {
    $token = new OAuthToken('some-token', 60);

    expect($token->getToken())->toBe('some-token');
    expect($token->getExpirationSeconds())->toBe(60);
    expect($token->getExpiration())->not->toBeNull();

    $expected = Carbon::now()->addSeconds(60)->getTimestamp();
    $actual = $token->getExpiration()?->getTimestamp();

    expect($actual)->toBeGreaterThanOrEqual($expected - 1);
    expect($actual)->toBeLessThanOrEqual($expected + 1);
});

it('allows expiration to be null', function (): void {
    $token = new OAuthToken('some-token');

    expect($token->getToken())->toBe('some-token');
    expect($token->getExpirationSeconds())->toBeNull();
    expect($token->getExpiration())->not->toBeNull();
});

it('throws when expiresIn is negative', function (): void {
    new OAuthToken('some-token', -1);
})->throws(\InvalidArgumentException::class, 'expiresIn has to be an integer > 0');
