<?php
namespace PSN\Friends;

define("USERS_URL", "https://us-prof.np.community.playstation.net/userProfile/v1/users/");

class Friend
{
    private $oauth;

    public function __construct($AccessToken)
    {
        $this->oauth = $AccessToken;
    }

    public function MyFriends($Filter = "online", $Limit = 36)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, USERS_URL . "me/friends/profiles2?fields=onlineId,avatarUrls,plus,trophySummary(@default),isOfficiallyVerified,personalDetail(@default,profilePictureUrls),primaryOnlineStatus,presences(@titleInfo,hasBroadcastData)&sort=name-onlineId&userFilter=" . $Filter ."&avatarSizes=m&profilePictureSizes=m&offset=0&limit=" . $Limit);

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

        curl_setopt($ch, CURLOPT_URL, USERS_URL . $PSN . "/profile2?fields=npId,onlineId,avatarUrls,plus,aboutMe,languagesUsed,trophySummary(@default,progress,earnedTrophies),isOfficiallyVerified,personalDetail(@default,profilePictureUrls),personalDetailSharing,personalDetailSharingRequestMessageFlag,primaryOnlineStatus,presences(@titleInfo,hasBroadcastData),friendRelation,requestMessageFlag,blocking,mutualFriendsCount,following,followerCount,friendsCount,followingUsersCount&avatarSizes=m,xl&profilePictureSizes=m,xl&languagesUsedLanguageSet=set3&psVitaTitleIcon=circled&titleIconSize=s");

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
    
    public function GetFriendsOfFriend($PSN, $Limit = 36)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, USERS_URL . $PSN ."/friends/profiles2?fields=onlineId,avatarUrls,plus,trophySummary(@default),isOfficiallyVerified,personalDetail(@default,profilePictureUrls),primaryOnlineStatus,presences(@titleInfo,hasBroadcastData)&sort=name-onlineId&avatarSizes=m&profilePictureSizes=m,xl&extendPersonalDetailTarget=true&offset=0&limit=" . $Limit);

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
