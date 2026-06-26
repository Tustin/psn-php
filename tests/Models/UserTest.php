<?php

use Tustin\PlayStation\Models\User;

it('builds user from object without extra fetch', function (): void {
    $user = User::fromObject((object) [
        'accountId' => '123',
        'onlineId' => 'mock-user',
        'avatars' => [
            ['size' => 'l', 'url' => 'https://avatar/l.png'],
            ['size' => 's', 'url' => 'https://avatar/s.png'],
        ],
        'presences' => [
            ['onlineStatus' => 'online'],
        ],
    ]);

    expect($user->accountId())->toBe('123');
    expect($user->onlineId())->toBe('mock-user');
    expect($user->avatarUrl())->toBe('https://avatar/l.png');
    expect($user->isOnline())->toBeTrue();
});

it('fetches user profile lazily when cache is empty', function (): void {
    [, $history] = $this->mockClient([
        $this->jsonResponse([
            'onlineId' => 'lazy-user',
            'avatars' => [
                ['size' => 'xl', 'url' => 'https://avatar/xl.png'],
            ],
            'presences' => [
                ['onlineStatus' => 'offline'],
            ],
        ]),
    ]);

    $user = new User('456');

    expect($user->onlineId())->toBe('lazy-user');
    expect($user->avatarUrl())->toBe('https://avatar/xl.png');
    expect($user->isOnline())->toBeFalse();
    expect($history->entries)->toHaveCount(1);
    expect((string) $history->entries[0]['request']->getUri())->toContain('userProfile/v1/internal/users/456/profiles');
});
