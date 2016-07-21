<?php
namespace PSN;

class User
{
    private $oauth;
    private $refresh_token;

    public function __construct($tokens)
    {
        $this->oauth = $tokens["oauth"];
        $this->refresh_token = $tokens["refresh"];
    }

    //Pulls info of the current logged in user
    public function Me()
    {
        $URL = USERS_URL . "me/profile2?fields=npId,onlineId,avatarUrls,plus,aboutMe,languagesUsed,trophySummary(@default,progress,earnedTrophies),isOfficiallyVerified,personalDetail(@default,profilePictureUrls),personalDetailSharing,personalDetailSharingRequestMessageFlag,primaryOnlineStatus,presences(@titleInfo,hasBroadcastData),friendRelation,requestMessageFlag,blocking,mutualFriendsCount,following,followerCount,friendsCount,followingUsersCount&avatarSizes=m,xl&profilePictureSizes=m,xl&languagesUsedLanguageSet=set3&psVitaTitleIcon=circled&titleIconSize=s";

        $headers = array(
            'Authorization: Bearer ' . $this->oauth,
        );

        $response = \Utilities::SendRequest($URL, $headers, false, null, "GET", null);

        $data = json_decode($response['body'], false);

        return $data;
    }

    //Blocks a user based on their PSN
    public function Block($PSN)
    {
        $headers = array(
            'Authorization: Bearer ' . $this->oauth,
            'Content-Type: application/json; charset=utf-8'
        );
        $response = \Utilities::SendRequest(USERS_URL . $this->Me()->profile->onlineId . "/blockList/" . $PSN . "?blockingUsersLimitType=1", $headers, false, null, "POST", null);

        $data = json_decode($response['body'], false);

        return $data;
    }

    //Unblocks a user based on their PSN, assuming they're already blocked by the user.
    public function Unblock($PSN)
    {
        $headers = array(
            'Authorization: Bearer ' . $this->oauth,
            'Content-Type: application/json; charset=utf-8'
        );

        $response = \Utilities::SendRequest(USERS_URL . $this->Me()->profile->onlineId . "/blockList/" . $PSN . "?blockingUsersLimitType=1", $headers, false, null, "DELETE", null);

        $data = json_decode($response['body'], false);

        return $data;
    }

    //Grabs the recent activity of the PSN user passed.
    public function GetUserActivity($PSN)
    {
        $headers = array(
            'Authorization: Bearer ' . $this->oauth,
            'Content-Type: application/json; charset=utf-8'
        );
        $URL = ACTIVITY_URL . $PSN . "/feed/0?includeComments=true&includeTaggedItems=true&filters=PURCHASED&filters=RATED&filters=VIDEO_UPLOAD&filters=SCREENSHOT_UPLOAD&filters=PLAYED_GAME&filters=WATCHED_VIDEO&filters=TROPHY&filters=BROADCASTING&filters=LIKED&filters=PROFILE_PIC&filters=FRIENDED&filters=CONTENT_SHARE&filters=IN_GAME_POST&filters=RENTED&filters=SUBSCRIBED&filters=FIRST_PLAYED_GAME&filters=IN_APP_POST&filters=APP_WATCHED_VIDEO&filters=SHARE_PLAYED_GAME&filters=VIDEO_UPLOAD_VERIFIED&filters=SCREENSHOT_UPLOAD_VERIFIED&filters=SHARED_EVENT&filters=JOIN_EVENT&filters=TROPHY_UPLOAD&filters=FOLLOWING&filters=RESHARE";

        $response = \Utilities::SendRequest($URL, $headers, false, null, "GET", null);

        $data = json_decode($response['body'], false);

        return $data;
    }
}
