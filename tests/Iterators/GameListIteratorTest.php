<?php

use Tustin\PlayStation\Iterators\GameListIterator;
use Tustin\PlayStation\Models\UserGameTitle;

it('iterates game list and paginates with offset', function (): void {
    [, $history] = $this->mockClient([
        $this->jsonResponse([
            'totalItemCount' => 3,
            'titles' => [
                ['titleId' => 'TITLE_1', 'name' => 'Game 1'],
                ['titleId' => 'TITLE_2', 'name' => 'Game 2'],
            ],
        ]),
        $this->jsonResponse([
            'totalItemCount' => 3,
            'titles' => [
                ['titleId' => 'TITLE_3', 'name' => 'Game 3'],
            ],
        ]),
    ]);

    $iterator = new GameListIterator('account-id', 2);

    expect($iterator->current())->toBeInstanceOf(UserGameTitle::class);
    expect($iterator->current()->id())->toBe('TITLE_1');
    expect($history->entries)->toHaveCount(1);

    $iterator->next();
    expect($iterator->current()->id())->toBe('TITLE_2');
    expect($history->entries)->toHaveCount(1);

    $iterator->next();
    expect($iterator->current()->id())->toBe('TITLE_3');
    expect($history->entries)->toHaveCount(2);

    $secondRequest = (string) $history->entries[1]['request']->getUri();
    expect($secondRequest)->toContain('offset=2');
});
