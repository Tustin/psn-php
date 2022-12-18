<?php
namespace Tustin\PlayStation\Factory;

use Iterator;
use GuzzleHttp\Client;
use IteratorAggregate;
use Tustin\PlayStation\Api;
use Tustin\PlayStation\Traits\HasUser;
use Tustin\PlayStation\Model\UserGameTitle;
use Tustin\PlayStation\Iterator\UserGameListIterator;
use Tustin\PlayStation\Interfaces\FactoryInterface;

class UserGameListFactory extends Api implements IteratorAggregate, FactoryInterface
{
    use HasUser;

    public function __construct(Client $client)
    {
        parent::__construct($client);
    }

    /**
     * Gets the iterator and applies any filters.
     */
    public function getIterator(): Iterator
    {
        $iterator = new UserGameListIterator($this);

        return $iterator;
    }

    /**
     * Gets the first game entry in the collection.
     */
    public function first(): UserGameTitle
    {
        return $this->getIterator()->current();
    }
}