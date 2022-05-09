<?php

namespace Tests\Client\Factories;

class StoreTestFactory
{
    /**
     * Return dummy data for store products
     *
     * @param int $amount
     * @return array
     * @throws \Exception
     */
    public function emptyDataProducts(int $amount): array
    {
        $items = [];

        for ($i = 0 ; $i < $amount ; $i++) {
            $item = [
                'id' => random_int(1111, 9999),
                'conceptProductMetadata' => [
                    'id' => random_int(1111, 9999),
                    'conceptId' => random_int(1111, 9999),
                ]
            ];

            $items[] = json_decode(json_encode($item, JSON_THROW_ON_ERROR), false, 512, JSON_THROW_ON_ERROR);
        }

        return $items;
    }
}