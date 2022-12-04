<?php

namespace Tustin\PlayStation\Model;

use GuzzleHttp\Client;
use Tustin\PlayStation\Model;
use Tustin\PlayStation\Factory\UsersFactory;
use Tustin\PlayStation\Factory\GameListFactory;
use Tustin\PlayStation\Factory\FriendsListFactory;
use Tustin\PlayStation\Model\Trophy\TrophySummary;
use Tustin\PlayStation\Factory\TrophyTitlesFactory;

class User extends Model
{
    /**
     * Constructs a new user object.
     *
     * @param UsersFactory $usersFactory
     * @param string $accountId
     */
    public function __construct(Client $client, private string $accountId)
    {
        parent::__construct($client);
    }

    public static function fromObject(Client $client, object $data)
    {
        $instance = new User($client, $data->accountId);
        $instance->setCache($data);

        return $instance;
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
     * 
     * @return string
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
     *
     * @return TrophySummary
     */
    public function trophySummary(): TrophySummary
    {
        return new TrophySummary($this);
    }

    /**
     * Gets online ID.
     *
     * @return string
     */
    public function onlineId(): string
    {
        return $this->pluck('onlineId');
    }

    /**
     * Gets the about me.
     *
     * @return string
     */
    public function aboutMe(): string
    {
        return $this->pluck('aboutMe');
    }

    /**
     * Gets the user's account ID.
     *
     * @return string
     */
    public function accountId(): string
    {
        return $this->accountId;
    }

    /**
     * This property is only returned in some API responses (namely the user search response), which can make this value inconsistent.
     * If this is needed, maybe we can have a setter method for setting this value? It would be a bit nicer than polluting the constructor.
     * Tustin - Nov 11, 2021.
     * 
     * @deprecated v3.0.1
     * 
     * @ignore
     *
     * @return string
     */
    public function country(): string
    {
        return '';
    }

    /**
     * Returns all the available avatar URL sizes.
     * 
     * Each array key is the size of the image.
     *
     * @return array
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
     *
     * @return string
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
     *
     * @return boolean
     */
    public function isBlocking(): bool
    {
        return $this->pluck('blocking');
    }

    /**
     * Get the user's follower count.
     *
     * @return integer
     */
    public function followerCount(): int
    {
        return $this->pluck('followerCount');
    }

    /**
     * Check if the client is following the user.
     *
     * @return boolean
     */
    public function isFollowing(): bool
    {
        return $this->pluck('following');
    }

    /**
     * Check if the user is verified.
     *
     * @return boolean
     */
    public function isVerified(): bool
    {
        return $this->pluck('isOfficiallyVerified');
    }

    /**
     * Gets all the user's languages.
     *
     * @return array
     */
    public function languages(): array
    {
        return $this->pluck('languagesUsed');
    }

    /**
     * Gets mutual friend count.
     * 
     * Returns -1 if current profile is the logged in user.
     *
     * @return integer
     */
    public function mutualFriendCount(): int
    {
        return $this->pluck('mutualFriendsCount');
    }

    /**
     * Checks if the client has any mutual friends with the user. 
     *
     * @return boolean
     */
    public function hasMutualFriends(): bool
    {
        return $this->mutualFriendCount() > 0;
    }

    /**
     * Checks if the client is close friends with the user.
     *
     * @return boolean
     */
    public function isCloseFriend(): bool
    {
        return $this->pluck('personalDetail') !== null;
    }

    /**
     * Checks if the client has a pending friend request with the user.
     * 
     * @TODO: Check if this works both ways.
     *
     * @return boolean
     */
    public function hasFriendRequested(): bool
    {
        return $this->pluck('friendRelation') === 'requesting';
    }

    /**
     * Checks if the user is currently online.
     *
     * @return boolean
     */
    public function isOnline(): bool
    {
        return $this->pluck('presences.0.onlineStatus') === 'online';
    }

    /**
     * Checks if the user has PlayStation Plus.
     *
     * @return boolean
     */
    public function hasPlus(): bool
    {
        return $this->pluck('isPlus');
    }

    /**
     * Fetches the user's profile information from the API.
     *
     * @return object
     */
    public function fetch(): object
    {
        return $this->get('userProfile/v1/internal/users/' . $this->accountId . '/profiles');
    }
}
