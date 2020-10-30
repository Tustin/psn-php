<?php

namespace Tustin\PlayStation\Factory;

use Tustin\PlayStation\Api\Api;
use Tustin\PlayStation\Model\User;
use Tustin\PlayStation\Iterator\UsersSearchIterator;

class UsersFactory extends Api
{
    /**
     * Searches for a user.
     *
     * @param string $query
     * @param array $searchFields
     * @return UsersSearchIterator
     */
    public function search(string $query) : UsersSearchIterator
    {
        return new UsersSearchIterator($this, $query);
    }

    /**
     * Find a specific user's profile by their accountId.
     *
     * @param string $accountId
     * @return User
     */
    public function find(string $accountId) : User
    {
        return new User($this, $accountId);
    }

    /**
     * Get the logged in user's profile.
     *
     * @return User
     */
    public function me() : User
    {
        return new User($this, 'me');
    }
}