<?php

namespace Tustin\PlayStation\Factory;

use Tustin\PlayStation\Api;
use Tustin\PlayStation\Client;
use Tustin\PlayStation\Model\User;
use Tustin\PlayStation\Interfaces\FactoryInterface;
use Tustin\PlayStation\Iterator\UsersSearchIterator;

class UsersFactory extends Api implements FactoryInterface
{
    public function __construct(Client $client)
    {
        parent::__construct($client->getHttpClient());
    }

    /**
     * begin a search for users matching the given query.
     */
    public function search(string $query, int $limit = 50): UsersSearchIterator
    {
        return new UsersSearchIterator($this, query: $query, limit: $limit);
    }

    /**
     * Get a user by account id.
     */
    public function find(string $accountId): User
    {
        return new User($accountId);
    }

    /**
     * Get the logged in user's profile.
     */
    public function me(): User
    {
        // Resolve account id (This is required even for the logged in user, `me` does not work here (afaik))
        $response = $this->get('https://dms.api.playstation.com/api/v1/devices/accounts/me');

        return new User($response->accountId);
    }
}
