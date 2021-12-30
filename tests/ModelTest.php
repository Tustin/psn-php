<?php

namespace Tests;

use GuzzleHttp\Client;
use Tustin\PlayStation\Model;
use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{
    /** @var Model */
    private $model;

    public function testItShouldPluck(): void
    {
        $this->assertNotEmpty($this->model->pluck('property'));
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = $this->getMockForAbstractClass(Model::class, [$this->createMock(Client::class)]);
    }
}
