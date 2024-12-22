<?php

namespace Tustin\PlayStation\Factory;

use Tustin\PlayStation\Api;
use Tustin\PlayStation\Client;
use Tustin\PlayStation\Model\User;
use Tustin\PlayStation\Interfaces\FactoryInterface;
use Tustin\PlayStation\Iterator\UsersSearchIterator;

class Users extends Api implements FactoryInterface
{
    public function __construct()
    {
        parent::__construct(Client::getInstance()->getHttpClient());
    }

    public static function where(string $psn, int $limit = 50): UsersSearchIterator
    {
        return (new static)
            ->search($psn, $limit);
    }

    /**
     * begin a search for users matching the given query.
     */
    public function search(string $query, int $limit = 50): UsersSearchIterator
    {
        return new UsersSearchIterator($query, limit: $limit);
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
