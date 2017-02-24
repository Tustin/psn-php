<?php
namespace PSN;

class Friend
{
    private $oauth;
    private $refresh_token;

    public function __construct($tokens)
    {
        $this->oauth = $tokens["oauth"];
        $this->refresh_token = $tokens["refresh"];
    }
    
    //Grabs the logged in user's friends, with a status filter and the amount of users to return.
    public function MyFriends($Filter = "online", $Limit = 36)
    {
        $headers = array(
            'Authorization: Bearer ' . $this->oauth
        );
        $response = \Utilities::SendRequest(USERS_URL . "me/friends/profiles2?fields=onlineId,avatarUrls,following,friendRelation,isOfficiallyVerified,personalDetail(@default,profilePictureUrls),personalDetailSharing,plus,presences(@titleInfo,hasBroadcastData,lastOnlineDate),primaryOnlineStatus,trophySummary(@default)&sort=name-onlineId&userFilter=" . $Filter ."&avatarSizes=m&profilePictureSizes=m&offset=0&limit=" . $Limit, $headers, false, null, "GET", null);

        $data = json_decode($response['body'], false);

        return $data;
    }

    //Gets info of a user.
    public function GetInfo($PSN)
    {
        $URL = USERS_URL . $PSN . "/profile2?fields=npId,onlineId,avatarUrls,plus,aboutMe,languagesUsed,trophySummary(@default,progress,earnedTrophies),isOfficiallyVerified,personalDetail(@default,profilePictureUrls),personalDetailSharing,personalDetailSharingRequestMessageFlag,primaryOnlineStatus,presences(@titleInfo,hasBroadcastData),friendRelation,requestMessageFlag,blocking,mutualFriendsCount,following,followerCount,friendsCount,followingUsersCount&avatarSizes=m,xl&profilePictureSizes=m,xl&languagesUsedLanguageSet=set3&psVitaTitleIcon=circled&titleIconSize=s";

        $headers = array(
            'Authorization: Bearer ' . $this->oauth
        );

        $response = \Utilities::SendRequest($URL, $headers, false, null, "GET", null);

        $data = json_decode($response['body'], false);

        return $data;
    }

    //Grabs the friends of a public profile or someone on your friends list.
    public function GetFriendsOfFriend($PSN, $Limit = 36)
    {
        $headers = array(
            'Authorization: Bearer ' . $this->oauth
        );

        $response = \Utilities::SendRequest(USERS_URL . $PSN ."/friends/profiles2?fields=onlineId,avatarUrls,plus,trophySummary(@default),isOfficiallyVerified,personalDetail(@default,profilePictureUrls),primaryOnlineStatus,presences(@titleInfo,hasBroadcastData)&sort=name-onlineId&avatarSizes=m&profilePictureSizes=m,xl&extendPersonalDetailTarget=true&offset=0&limit=" . $Limit, $headers, false, null, "GET", null);

        $data = json_decode($response['body'], false);

        return $data;
    }

    //Grabs the mutual friends.
    public function GetMutualFriends($PSN, $Limit = 36)
    {
        $headers = array(
            'Authorization: Bearer ' . $this->oauth
        );

        $response = \Utilities::SendRequest(USERS_URL . $PSN ."/friends/profiles2?fields=onlineId,avatarUrls,plus,trophySummary(@default),isOfficiallyVerified,personalDetail(@default,profilePictureUrls),primaryOnlineStatus,presences(@titleInfo,hasBroadcastData)&sort=name-onlineId&userFilter=mutualFriends&avatarSizes=m&profilePictureSizes=m,xl&extendPersonalDetailTarget=true&offset=0&limit=" . $Limit, $headers, false, null, "GET", null);

        $data = json_decode($response['body'], false);

        return $data;
    }

    //Compare all games user trophies
    public function CompareUserTrophies($PSN, $Limit = 36)
    {
        $URL = TROPHY_URL . "trophyTitles?fields=@default&npLanguage=en&iconSize=m&platform=PS3,PSVITA,PS4&offset=0&comparedUser=" . $PSN . "&limit=" . $Limit;

        $headers = array(
            'Authorization: Bearer ' . $this->oauth
        );

        $response = \Utilities::SendRequest($URL, $headers, false, null, "GET", null);

        $data = json_decode($response['body'], false);

        return $data;
    }

    //Compare specific game trophies - Short Version
    public function CompareGameUserTrophiesSimple($NPid ,$PSN)
    {
        $URL = TROPHY_URL . "trophyTitles/" . $NPid . "/trophyGroups/?npLanguage=en&comparedUser=" . $PSN;

        $headers = array(
            'Authorization: Bearer ' . $this->oauth
        );

        $response = \Utilities::SendRequest($URL, $headers, false, null, "GET", null);

        $data = json_decode($response['body'], false);

        return $data;
    }

    //Compare specific game trophies - Extended Version
    public function CompareGameUserTrophiesExtended($NPid ,$PSN)
    {
        $URL = TROPHY_URL . "trophyTitles/" . $NPid . "/trophyGroups/default/trophies?npLanguage=en&comparedUser=" . $PSN;

        $headers = array(
            'Authorization: Bearer ' . $this->oauth
        );

        $response = \Utilities::SendRequest($URL, $headers, false, null, "GET", null);

        $data = json_decode($response['body'], false);

        return $data;
    }

    //Adds a user to your friends list, with an optional message.
    public function Add($PSN, $RequestMessage = "")
    {
        //Since we're not inside the User class, we need to grab the logged in user's onlineId
        $tokens = array(
            'oauth' => $this->oauth,
            'refresh' => $this->refresh
        );

        $_user = new \PSN\User($tokens);
        $_onlineId = $_user->Me()->profile->onlineId;

        $headers = array(
            'Authorization: Bearer ' . $this->oauth,
            'Content-Type: application/json; charset=utf-8'
        );

        $message = array(
            "requestMessage" => $RequestMessage
        );
        $request_message = !empty($RequestMessage) ? json_encode($message) : '{}';

        $response = \Utilities::SendRequest(USERS_URL . $_onlineId . "/friendList/" . $PSN, $headers, false, null, "POST", $request_message);

        $data = json_decode($response['body'], false);

        return $data;
    }

    //Removes a friend from your friends list.
    public function Remove($PSN)
    {
        $tokens = array(
            'oauth' => $this->oauth,
            'refresh' => $this->refresh
        );
        $_user = new \PSN\User($tokens);
        $_onlineId = $_user->Me()->profile->onlineId;

        $headers = array(
            'Authorization: Bearer ' . $this->oauth,
            'Content-Type: application/json; charset=utf-8'
        );

        $response = \Utilities::SendRequest(USERS_URL . $_onlineId . "/friendList/" . $PSN, $headers, false, null, "DELETE", null);

        $data = json_decode($response['body'], false);

        return $data;
    }
}
