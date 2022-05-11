<?php

namespace Tests;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Tustin\PlayStation\Model\Store\Concept;
use function PHPUnit\Framework\assertIsString;

class GameList extends TestCase
{
    /** @test */
    public function concept_pluck_with_null_data()
    {
        $mock = $this->getMockBuilder(Concept::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['pluck'])
            ->getMock();

        // Some concept fetch from gameList has no properties
        $mock->method('pluck')->willReturn(null);

        $productId = $mock->productId();
        $name = $mock->name();
        $conceptId = $mock->publisher();

        $this->assertIsString($productId);
        $this->assertIsString($name);
        $this->assertIsString($conceptId);
    }
}