<?php

namespace Tests\UseCases;

use PHPUnit\Framework\TestCase;
use Tests\Client\Factories\StoreTestFactory;
use Tustin\PlayStation\Client;
use Tustin\PlayStation\Factory\StoreFactory;
use Tustin\PlayStation\Iterator\StoreSearchIterator;
use Tustin\PlayStation\Model\Store\Concept;

class StoreTest extends TestCase
{
    /**
     * Fetches an n amount from the store and update the cache when necessary.
     *
     * @return void
     * @throws \Exception
     *
     * @test
     */
    public function store_search_iterator(): void
    {
        $factory = $this->createMock(StoreFactory::class);

        $products = $this->getMockBuilder(StoreSearchIterator::class)
            ->setConstructorArgs([$factory, 'searchTerm'])
            ->onlyMethods(['access'])
            ->getMock();

        $items = (new StoreTestFactory)->emptyDataProducts(100);
        $products->update(count($items), $items, null);

        while($products->valid()) {
            $products->next();
        }

        echo 'Executed ' . $products->key() . ' times.';
        $this->assertEquals(count($items), $products->key());
    }
}