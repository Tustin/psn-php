<?php

use Tustin\PlayStation\Client;
use Tustin\PlayStation\Enums\TrophyServiceName;
use Tustin\PlayStation\Factories\CloudMediaGallery;
use Tustin\PlayStation\Factories\Groups;
use Tustin\PlayStation\Factories\Users;
use Tustin\PlayStation\Iterators\StoreSearchIterator;
use Tustin\PlayStation\Models\Media;
use Tustin\PlayStation\Models\Trophy\TrophyTitle;

it('logs in with npsso and stores tokens', function (): void {
    [$client, $history] = $this->mockClient([
        $this->jsonResponse(null, 302, ['Location' => 'https://redirect.local?code=AUTH-CODE']),
        $this->jsonResponse([
            'access_token' => 'access-token',
            'expires_in' => 3600,
            'refresh_token' => 'refresh-token',
            'refresh_token_expires_in' => 7200,
        ]),
    ]);

    $token = $client->loginWithNpsso('my-npsso');

    expect($token->getToken())->toBe('access-token');
    expect($client->getRefreshToken()?->getToken())->toBe('refresh-token');
    expect($history->entries)->toHaveCount(2);
    expect((string) $history->entries[0]['request']->getUri())->toContain('authz/v3/oauth/authorize');
    expect((string) $history->entries[1]['request']->getUri())->toContain('authz/v3/oauth/token');
});

it('logs in with refresh token', function (): void {
    [$client, $history] = $this->mockClient([
        $this->jsonResponse([
            'access_token' => 'access-token-2',
            'expires_in' => 3600,
            'refresh_token' => 'refresh-token-2',
            'refresh_token_expires_in' => 7200,
        ]),
    ]);

    $token = $client->loginWithRefreshToken('existing-refresh-token');

    expect($token->getToken())->toBe('access-token-2');
    expect($client->getRefreshToken()?->getToken())->toBe('refresh-token-2');
    expect($history->entries)->toHaveCount(1);
    expect((string) $history->entries[0]['request']->getUri())->toContain('authz/v3/oauth/token');
});

it('throws when oauth authorize response code is not 302', function (): void {
    [$client] = $this->mockClient([
        $this->jsonResponse(null, 301, ['Location' => 'https://redirect.local?code=AUTH-CODE']),
    ]);

    $client->loginWithNpsso('my-npsso');
})->throws(\Exception::class, 'Incorrect response code from oauth/authorize.');

it('throws when oauth authorize response is missing location header', function (): void {
    [$client] = $this->mockClient([
        $this->jsonResponse(null, 302, []),
    ]);

    $client->loginWithNpsso('my-npsso');
})->throws(\Exception::class, 'Missing redirect location from oauth/authorize.');

it('throws when oauth authorize response location is missing code', function (): void {
    [$client] = $this->mockClient([
        $this->jsonResponse(null, 302, ['Location' => 'https://redirect.local?foo=bar']),
    ]);

    $client->loginWithNpsso('my-npsso');
})->throws(\Exception::class, 'Missing code from oauth/authorize.');

it('returns expected factories and models', function (): void {
    [$client] = $this->mockClient([
        $this->jsonResponse([
            'domainResponses' => [[
                'totalResultCount' => 0,
                'results' => [],
                'next' => '',
            ]],
        ]),
    ]);

    expect($client->users())->toBeInstanceOf(Users::class);
    expect($client->trophyTitle('NPWR12345_00', TrophyServiceName::Trophy))->toBeInstanceOf(TrophyTitle::class);
    expect($client->groups())->toBeInstanceOf(Groups::class);
    expect($client->media('ugc-id'))->toBeInstanceOf(Media::class);
    expect($client->cloudMediaGallery())->toBeInstanceOf(CloudMediaGallery::class);
    expect($client->store())->toBeInstanceOf(StoreSearchIterator::class);
});
