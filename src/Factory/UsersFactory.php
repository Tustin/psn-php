<?php

namespace Tustin\PlayStation\Factory;

use Tustin\PlayStation\Api;
use Tustin\PlayStation\Model\User;
use Tustin\PlayStation\Interfaces\FactoryInterface;
use Tustin\PlayStation\Iterator\UsersSearchIterator;

class UsersFactory extends Api implements FactoryInterface
{
    /**
     * Searches for a user.
     *
     * @param string $query
     * @param array $searchFields
     * @return UsersSearchIterator
     */
    public function search(string $query): UsersSearchIterator
    {
        return new UsersSearchIterator($this, $query);
    }

    /**
     * Find a specific user's profile by their accountId.
     *
     * @param string $accountId
     * @return User
     */
    public function find(string $accountId): User
    {
        return new User($this->getHttpClient(), $accountId);
    }

    /**
     * Get the logged in user's profile.
     *
     * @return User
     */
    public function me(): User
    {
        // Resolve account id
        $response = $this->get('https://dms.api.playstation.com/api/v1/devices/accounts/me');
        return new User($this->getHttpClient(), $response->accountId);
    }
}