<?php

namespace PlayStation\Api;

use PlayStation\Client;
use PlayStation\Api\Trophy;

class User extends AbstractApi {

    public const USERS_ENDPOINT = 'https://us-prof.np.community.playstation.net/userProfile/v1/users/%s/';

    private $onlineId;
    private $onlineIdParameter;

    public function __construct(Client $client, string $onlineId = null) 
    {
        parent::__construct($client);

        $this->onlineId = $onlineId;
        $this->onlineIdParameter = $this->onlineId ?? "me";
    }

    public function getInfo() 
    {
        return $this->get(sprintf(self::USERS_ENDPOINT . 'profile2', $this->onlineIdParameter), [
            'fields' => 'npId,onlineId,accountId,avatarUrls,plus,aboutMe,languagesUsed,trophySummary(@default,progress,earnedTrophies),isOfficiallyVerified,personalDetail(@default,profilePictureUrls),personalDetailSharing,personalDetailSharingRequestMessageFlag,primaryOnlineStatus,presences(@titleInfo,hasBroadcastData),friendRelation,requestMessageFlag,blocking,mutualFriendsCount,following,followerCount,friendsCount,followingUsersCount&avatarSizes=m,xl&profilePictureSizes=m,xl&languagesUsedLanguageSet=set3&psVitaTitleIcon=circled&titleIconSize=s'
        ]);
    }

    public function getOnlineId() 
    {
        return $this->onlineId;
    }
    
    public function add(string $requestMessage = null)
    {
        if ($this->onlineId === null) return;

        $data = ($requestMessage === null) ? new \stdClass() : [
            "requestMessage" => $requestMessage
        ];

        return $this->postJson(sprintf(self::USERS_ENDPOINT . 'friendList/%s', $this->client->getOnlineId(), $this->onlineId), $data);
    }

    public function remove() 
    {
        if ($this->onlineId === null) return;

        return $this->delete(sprintf(self::USERS_ENDPOINT . 'friendList/%s', $this->client->getOnlineId(), $this->onlineId));
    }

    public function block()
    {
        if ($this->onlineId === null) return;

        return $this->post(sprintf(self::USERS_ENDPOINT . 'blockList/%s', $this->client->getOnlineId(), $this->onlineId), null);
    }

    public function unblock() 
    {
        if ($this->onlineId === null) return;

        return $this->delete(sprintf(self::USERS_ENDPOINT . 'blockList/%s', $this->client->getOnlineId(), $this->onlineId));
    }

    public function getFriends($filter = 'online', $limit = 36)
    {
        $result = [];

        $friends = $this->get(sprintf(self::USERS_ENDPOINT . 'friends/profiles2', $this->onlineIdParameter), [
            'fields' => 'onlineId',
            'limit' => $limit,
            'sort' => 'name-onlineId'
        ]);
        
        foreach ($friends->profiles as $friend) {
            $result[] = new self($this->client, $friend->onlineId);
        }

        return $result;
    }

    public function trophies() 
    {
        return new Trophy($this->client, $this);
    }

    public function getMessageThreads(int $limit = 20) : array
    {
        $messageThreads = [];
        $threads = $this->client->getMessageThreads($limit);
        
        // If this user is the logged in account, just return all the message threads. 
        foreach ($threads->threads as $thread) {
            if ($this->onlineId === null) {
                $messageThreads[] = $thread;
                continue;
            }

            foreach ($thread->threadMembers as $member) {
                if (strtolower($member->onlineId) === strtolower($this->onlineId)) {
                    $messageThreads[] = $thread;
                }
            }
        }

  

        result:
        $returnMessages = [];
        foreach ($messageThreads as $thread) {
            $returnMessages[] = new MessageThread($this->client, $thread);
        }

        return $returnMessages;
    }
}