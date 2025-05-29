<?php

namespace Tustin\PlayStation\Factories;

use Tustin\PlayStation\Api;
use Tustin\PlayStation\Models\User;
use Tustin\PlayStation\Interfaces\FactoryInterface;
use Tustin\PlayStation\Iterators\UsersSearchIterator;

class Users extends Api implements FactoryInterface
{
    public function __construct() {}

    /**
     * Search for users matching the given PSN ID.
     */
    public static function where(string $psn, int $limit = 50): UsersSearchIterator
    {
        return (new static)
            ->search($psn, $limit);
    }

    /**
     * Performs a search for the given PSN and returns the first result.
     * 
     * Returns null if no user is found.
     */
    public static function firstWhere(string $psnId): ?User
    {
        return Users::where($psnId)->first();
    }

    /**
     * Search for users matching the given query.
     */
    public static function search(string $query, int $limit = 50): UsersSearchIterator
    {
        return new UsersSearchIterator($query, limit: $limit);
    }

    /**
     * Find a user by their account id.
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
