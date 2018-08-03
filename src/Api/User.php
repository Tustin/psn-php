<?php

namespace PlayStation\Api;

use PlayStation\Client;


class User extends AbstractApi {

    private const USERS_ENDPOINT = 'https://us-prof.np.community.playstation.net/userProfile/v1/users/%s/';

    private $onlineId;

    public function __construct(Client $client, string $onlineId = null) 
    {
        parent::__construct($client);

        $this->onlineId = $onlineId;
    }

    public function getInfo() 
    {
        return $this->get(sprintf(self::USERS_ENDPOINT . 'profile2', $this->onlineId === null ? 'me' : $this->onlineId), [
            'fields' => 'npId,onlineId,accountId,avatarUrls,plus,aboutMe,languagesUsed,trophySummary(@default,progress,earnedTrophies),isOfficiallyVerified,personalDetail(@default,profilePictureUrls),personalDetailSharing,personalDetailSharingRequestMessageFlag,primaryOnlineStatus,presences(@titleInfo,hasBroadcastData),friendRelation,requestMessageFlag,blocking,mutualFriendsCount,following,followerCount,friendsCount,followingUsersCount&avatarSizes=m,xl&profilePictureSizes=m,xl&languagesUsedLanguageSet=set3&psVitaTitleIcon=circled&titleIconSize=s'
        ]);
    }

}