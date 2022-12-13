<?php

namespace Tustin\PlayStation\Model;

use GuzzleHttp\Client;
use Tustin\PlayStation\Model;
use Tustin\PlayStation\Factory\GameListFactory;
use Tustin\PlayStation\Factory\FriendsListFactory;
use Tustin\PlayStation\Model\Trophy\TrophySummary;
use Tustin\PlayStation\Factory\TrophyTitlesFactory;

class User extends Model
{
    /**
     * The user's country.
     */
    private string $country;

    /**
     * Constructs a new user object.
     */
    public function __construct(Client $client, private string $accountId)
    {
        parent::__construct($client);
    }

    public static function fromObject(Client $client, object $data): self
    {
        $instance = new User($client, $data->accountId);
        $instance->setCache($data);

        return $instance;
    }

    /**
     * Sets the country for this user.
     */
    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get the trophy titles associated with this user's account.
     * 
     * @return TrophyTitlesFactory
     */
    public function trophyTitles(): TrophyTitlesFactory
    {
        return new TrophyTitlesFactory($this);
    }

    /**
     * Get the game list for this user's account.
     *
     * @return GameListFactory
     */
    public function gameList(): GameListFactory
    {
        return new GameListFactory($this);
    }

    /**
     * Gets the user's friends list.
     *
     * @return FriendsListFactory
     */
    public function friends(): FriendsListFactory
    {
        return new FriendsListFactory($this);
    }

    /**
     * Get the communication id (NPWR...) from a title id (CUSA...)
     * 
     * Only works for PS4/PS5 titles.
     * Doesn't work with PPSA... title ids.
     */
    public function titleIdToCommunicationId($npTitleId): string
    {
        $body = [
            'npTitleIds' => $npTitleId
        ];

        $results = $this->get('trophy/v1/users/' . $this->accountId() . '/titles/trophyTitles', $body);

        if (count($results->titles[0]->trophyTitles) == 0) {
            return '';
        }

        return $results->titles[0]->trophyTitles[0]->npCommunicationId;
    }

    /**
     * Gets the trophy summary for the user.
     */
    public function trophySummary(): TrophySummary
    {
        return new TrophySummary($this);
    }

    /**
     * Gets online ID.
     */
    public function onlineId(): string
    {
        return $this->pluck('onlineId');
    }

    /**
     * Gets the about me.
     */
    public function aboutMe(): string
    {
        return $this->pluck('aboutMe');
    }

    /**
     * Gets the user's account ID.
     */
    public function accountId(): string
    {
        return $this->accountId;
    }

    /**
     * Gets the user's country.
     * 
     * This property will only be available if the user was obtained via the user search endpoint.
     */
    public function country(): ?string
    {
        return $this->country;
    }

    /**
     * Returns all the available avatar URL sizes.
     * 
     * Each array key is the size of the image.
     */
    public function avatarUrls(): array
    {
        $urls = [];

        foreach ($this->pluck('avatars') as $avatar) {
            $urls[$avatar['size']] = $avatar['url'];
        }

        return $urls;
    }

    /**
     * Gets the avatar URL.
     * 
     * This should return the largest size available.
     */
    public function avatarUrl(): string
    {
        $sizes = ['xl', 'l', 'm', 's'];

        foreach ($sizes as $size) {
            if (array_key_exists($size, $this->avatarUrls())) {
                return $this->avatarUrls()[$size];
            }
        }

        // Could not find any of the sizes specified, just return the first one in the array.
        return current($this->avatarUrls());
    }

    /**
     * Check if client is blocking the user.
     */
    public function isBlocking(): bool
    {
        return $this->pluck('blocking');
    }

    /**
     * Get the user's follower count.
     */
    public function followerCount(): int
    {
        return $this->pluck('followerCount');
    }

    /**
     * Check if the client is following the user.
     */
    public function isFollowing(): bool
    {
        return $this->pluck('following');
    }

    /**
     * Check if the user is verified.
     */
    public function isVerified(): bool
    {
        return $this->pluck('isOfficiallyVerified');
    }

    /**
     * Gets all the user's languages.
     */
    public function languages(): array
    {
        return $this->pluck('languagesUsed');
    }

    /**
     * Gets mutual friend count.
     * 
     * Returns -1 if current profile is the logged in user.
     */
    public function mutualFriendCount(): int
    {
        return $this->pluck('mutualFriendsCount');
    }

    /**
     * Checks if the client has any mutual friends with the user. 
     */
    public function hasMutualFriends(): bool
    {
        return $this->mutualFriendCount() > 0;
    }

    /**
     * Checks if the client is close friends with the user.
     */
    public function isCloseFriend(): bool
    {
        return $this->pluck('personalDetail') !== null;
    }

    /**
     * Checks if the client has a pending friend request with the user.
     * 
     * @TODO: Check if this works both ways.
     */
    public function hasFriendRequested(): bool
    {
        return $this->pluck('friendRelation') === 'requesting';
    }

    /**
     * Checks if the user is currently online.
     */
    public function isOnline(): bool
    {
        return $this->pluck('presences.0.onlineStatus') === 'online';
    }

    /**
     * Checks if the user has PlayStation Plus.
     */
    public function hasPlus(): bool
    {
        return $this->pluck('isPlus');
    }

    /**
     * Fetches the user's profile information from the API.
     */
    public function fetch(): object
    {
        return $this->get('userProfile/v1/internal/users/' . $this->accountId . '/profiles');
    }
}
