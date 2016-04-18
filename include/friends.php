<?php
namespace PSN\Friends;

define("FRIENDS_URL", "https://us-prof.np.community.playstation.net/userProfile/v1/users/");
define("ACTIVITY_URL", "https://activity.api.np.km.playstation.net/activity/api/v1/users/");
define("INFO_URL", "https://us-prof.np.community.playstation.net/userProfile/v1/users/");

class Friend
{
    private $oauth;

    public function __construct($AccessToken)
    {
        $this->oauth = $AccessToken;
    }
    public function GetAll($Filter = "online", $Limit = 36)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, FRIENDS_URL . "me/friends/profiles2?fields=onlineId,avatarUrls,plus,trophySummary(@default),isOfficiallyVerified,personalDetail(@default,profilePictureUrls),primaryOnlineStatus,presences(@titleInfo,hasBroadcastData)&sort=name-onlineId&userFilter=" . $Filter ."&avatarSizes=m&profilePictureSizes=m&offset=0&limit=" . $Limit);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $headers = array(
            'Authorization: Bearer ' . $this->oauth,
        );

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec($ch);

        curl_close($ch);

        $data = json_decode($output, false);

        return $data;
    }

    public function GetInfo($PSN)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, INFO_URL . $PSN . "/profile2?fields=npId,onlineId,avatarUrls,plus,aboutMe,languagesUsed,trophySummary(@default,progress,earnedTrophies),isOfficiallyVerified,personalDetail(@default,profilePictureUrls),personalDetailSharing,personalDetailSharingRequestMessageFlag,primaryOnlineStatus,presences(@titleInfo,hasBroadcastData),friendRelation,requestMessageFlag,blocking,mutualFriendsCount,following,followerCount,friendsCount,followingUsersCount&avatarSizes=m,xl&profilePictureSizes=m,xl&languagesUsedLanguageSet=set3&psVitaTitleIcon=circled&titleIconSize=s");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $headers = array(
            'Authorization: Bearer ' . $this->oauth,
        );

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec($ch);

        curl_close($ch);

        $data = json_decode($output, false);

        return $data;
    }

    public function GetActivity($PSN)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, ACTIVITY_URL . $PSN . "/feed/0?includeComments=true&includeTaggedItems=true&filters=PURCHASED&filters=RATED&filters=VIDEO_UPLOAD&filters=SCREENSHOT_UPLOAD&filters=PLAYED_GAME&filters=WATCHED_VIDEO&filters=TROPHY&filters=BROADCASTING&filters=LIKED&filters=PROFILE_PIC&filters=FRIENDED&filters=CONTENT_SHARE&filters=IN_GAME_POST&filters=RENTED&filters=SUBSCRIBED&filters=FIRST_PLAYED_GAME&filters=IN_APP_POST&filters=APP_WATCHED_VIDEO&filters=SHARE_PLAYED_GAME&filters=VIDEO_UPLOAD_VERIFIED&filters=SCREENSHOT_UPLOAD_VERIFIED&filters=SHARED_EVENT&filters=JOIN_EVENT&filters=TROPHY_UPLOAD&filters=FOLLOWING&filters=RESHARE");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $headers = array(
            'Authorization: Bearer ' . $this->oauth,
        );

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec($ch);

        curl_close($ch);

        $data = json_decode($output, false);

        return $data;
    }
    public function GetFriends($PSN, $Limit = 36)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, FRIENDS_URL . $PSN ."/friends/profiles2?fields=onlineId,avatarUrls,plus,trophySummary(@default),isOfficiallyVerified,personalDetail(@default,profilePictureUrls),primaryOnlineStatus,presences(@titleInfo,hasBroadcastData)&sort=name-onlineId&avatarSizes=m&profilePictureSizes=m,xl&extendPersonalDetailTarget=true&offset=0&limit=" . $Limit);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $headers = array(
            'Authorization: Bearer ' . $this->oauth,
        );

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec($ch);

        curl_close($ch);

        $data = json_decode($output, false);

        return $data;
    }
}
