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
     */
    public function search(string $query): UsersSearchIterator
    {
        return new UsersSearchIterator($this, $query);
    }

    /**
     * Find a specific user's profile by their accountId.
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
        // @TODO: Can you just pass me as the accountId? Cant remember if that works or if I resolved accountId for other reasons.
        // Resolve account id
        $response = $this->get('https://dms.api.playstation.com/api/v1/devices/accounts/me');

        return new User($response->accountId);
    }
}
