<?php

use Tustin\PlayStation\ApiModel;

it('plucks nested values from cache', function (): void {
    $model = new class extends ApiModel {
        public function fetch(): object
        {
            return (object) [];
        }
    };

    $model->setCache((object) [
        'profile' => [
            'name' => 'josh',
            'meta' => [
                'level' => 5,
            ],
        ],
    ]);

    expect($model->pluck('profile.name'))->toBe('josh');
    expect($model->pluck('profile.meta.level'))->toBe(5);
});

it('fetches lazily when cache key is missing', function (): void {
    $model = new class extends ApiModel {
        public int $fetchCount = 0;

        public function fetch(): object
        {
            $this->fetchCount++;

            return (object) [
                'value' => 'loaded',
            ];
        }
    };

    expect($model->pluck('value'))->toBe('loaded');
    expect($model->fetchCount)->toBe(1);
    expect($model->pluck('value'))->toBe('loaded');
    expect($model->fetchCount)->toBe(1);
});

it('returns null when key is still missing after fetch', function (): void {
    $model = new class extends ApiModel {
        public function fetch(): object
        {
            return (object) [
                'other' => 'value',
            ];
        }
    };

    expect($model->pluck('missing'))->toBeNull();
});
