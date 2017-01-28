<?php
namespace PSN;

class Communities
{
    private $oauth;
    private $refresh_token;
    private $community_oauth;
    private $me;

    private $community_login_request = array(
        "npsso" => null,
        "device_profile" => "mobile",
        "grant_type" => "sso_cookie",
        "scope" => "psn:sceapp,user:account.get,user:account.settings.privacy.get,user:account.settings.privacy.update,user:account.realName.get,user:account.realName.update,kamaji:get_account_hash,kamaji:communities_mobileapp,kamaji:satchel,kamaji:game_list,capone:report_submission",
        "service_entity" => "urn:service-entity:psn",
        "ui" => "pr",
        "duid" => "0000000d000400808F4B3AA3301B4945B2E3636E38C0DDFC",
        "client_id" => "58f7f128-5325-41f1-bcff-7b590b7200cd",
        "client_secret" => "BFogDNpBInrYB8s0"
    );

    //This function generates a new oauth token whenever you create a new instance of this class.
    //Reason being is that Sony created a different scope of permissions that the community app needs to use community-related functions.
    //Without creating a new oauth token for these special permissions, you wouldn't be able to use community-related functions as the regular PSN app doesn't give the required permissions.
    private function Login($npsso)
    {
        $this->community_login_request['npsso'] = $npsso;

        $response = \Utilities::SendRequest(OAUTH_URL, null, false, null, "POST", http_build_query($this->community_login_request));

        $data = json_decode($response["body"], false);

        if (property_exists($data, "error")){
            throw new AuthException($response["body"]); 
        }

        return $data;
    }
    public function __construct($tokens)
    {
        $this->community_oauth = $this->Login($tokens['npsso'])->access_token;
        $this->oauth = $tokens["oauth"];
        $this->refresh_token = $tokens["refresh"];
    }

    public function GetCommunity($communityId, $limit = 100)
    {
        $headers = array(
            'Authorization: Bearer ' . $this->community_oauth
        );

        $response = \Utilities::SendRequest(COMMUNITIES_URL  . $communityId . "/members?limit=" . $limit, $headers);

        $data = json_decode($response['body'], false);

        return $data;
    }

    public function GetMyCommunities($limit = 100)
    {
        $headers = array(
            'Authorization: Bearer ' . $this->community_oauth,
        );

        $response = \Utilities::SendRequest(COMMUNITIES_URL ."?fields=backgroundImage%2CtilebackgroundImage%2Cdescription%2Cid%2Cmembers%2Cname%2CprofileImage%2Crole%2CunreadMessageCount%2Csessions%2Ctype%2Clanguage%2Ctimezone%2CtitleName%2C%20titleId%2CnameLastModifiedBy%2CdescriptionLastModifiedBy%2CgriefReportableItems%2CgameSessions%2Cparties%26includeFields%3DgameSessions%2Cparties&limit=" . $limit, $headers);

        $data = json_decode($response['body'], false);

        return $data;
    }
    public function GetCommunityThreads($communityId)
    {
        $headers = array(
            'Authorization: Bearer ' . $this->community_oauth
        );

        $response = \Utilities::SendRequest(COMMUNITIES_URL  . $communityId . "/threads", $headers);

        $data = json_decode($response['body'], false);

        return $data;
    }

    public function GetMessageReplies($communityId, $threadId, $replyId, $limit = 100)
    {

        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->community_oauth
        );


        $response = \Utilities::SendRequest(COMMUNITIES_URL  . $communityId . "/threads/" . $threadId . "/messages/" . $replyId . "/replies?sharedMedia=events&limit=" . $limit, $headers);

        $data = json_decode($response['body'], false);

        return $data;
    }

    public function GetCommunityInfo($communityId)
    {

        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->community_oauth
        );

        $response = \Utilities::SendRequest(COMMUNITIES_URL  . $communityId . "?includeFields=members(size)", $headers);

        $data = json_decode($response['body'], false);

        return $data;
    }

    public function PostMessageReply($communityId, $threadId, $replyId, $message)
    {

        //for onlineids it requires array
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->community_oauth
        );

        $body = array(
            'images' => array(),
            'message' => $message
        );

        $response = \Utilities::SendRequest(COMMUNITIES_URL  . $communityId . "/threads/" . $threadId . "/messages/" . $replyId . "/replies?action=create", $headers, false, null, "POST", json_encode($body));

        $data = json_decode($response['body'], false);

        return $data;
    }
    public function GetNotifications($communityId, $replies = FALSE, $wall = FALSE)
    {
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->community_oauth
        );

        $body = array(
            'repliesNotification' => $replies,
            'wallNotification' => $wall
        );

        $response = \Utilities::SendRequest(COMMUNITIES_URL  . $communityId . "/preferences", $headers, false, null, "POST", json_encode($body));

        $data = json_decode($response['body'], false);

        return $data;
    }
    public function GetMessagesInThread($communityId, $threadId, $limit = 100)
    {
        $headers = array(
            'Authorization: Bearer ' . $this->community_oauth
        );

        $response = \Utilities::SendRequest(COMMUNITIES_URL  . $communityId . "/threads/" . $threadId . "/messages?limit=" . $limit, $headers);

        $data = json_decode($response['body'], false);

        return $data;
    }
    public function SubmitNewMessage($communityId, $threadId, $message)
    {
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->community_oauth
        );
        //TODO: add image support
        $body = array(
            'message' => $message,
            'images' => []
        );

        $response = \Utilities::SendRequest(COMMUNITIES_URL  . $communityId . "/threads/" . $threadId . "/messages", $headers, false, null, "POST", json_encode($body));

        $data = json_decode($response['body'], false);

        return $data;

    }
    public function InviteToCommunity($communityId, $onlineIds)
    {
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->community_oauth
        );
        $body = array(
            'onlineIds' => array ()
        );
        if (is_array($onlineIds)) {
            $body['onlineIds'] = $onlineIds;
        } else {
            $body['onlineIds'][0] = $onlineIds;
        }

        $response = \Utilities::SendRequest(COMMUNITIES_URL  . $communityId . "/members", $headers, false, null, "POST", json_encode($body));

        $data = json_decode($response['body'], false);

        return $data;
    }
    public function SearchCommunitiesByGame($titleId, $limit = 10)
    {
        $headers = array(
            'Authorization: Bearer ' . $this->community_oauth
        );

        $response = \Utilities::SendRequest("https://communities.api.playstation.com/v1/recommendations?includeFields=backgroundImage%2CtilebackgroundImage%2Cdescription%2Cid%2Cmembers%2Cname%2CprofileImage%2Crole%2CunreadMessageCount%2Csessions%2Ctype%2Clanguage%2Ctimezone%2CtitleName%2C%20titleId%2CnameLastModifiedBy%2CdescriptionLastModifiedBy%2CgriefReportableItems%2CgameSessions%2Cparties&limit=" . $limit . "&state=basedOnOneOfMyGames&titleId=" . urlencode($titleId), $headers);

        $data = json_decode($response['body'], false);

        return $data;
    }
    public function SearchCommunities($SearchTXT, $limit = 10)
    {
        $headers = array(
            'Authorization: Bearer ' . $this->community_oauth
        );

        $response = \Utilities::SendRequest("https://communities.api.playstation.com/v1/search?query=" . urlencode($SearchTXT) . "&limit=" . $limit, $headers);

        $data = json_decode($response['body'], false);

        return $data;
    }
    public function DeleteMessage($communityId, $threadId, $messageId)
    {
        $headers = array(
            'Authorization: Bearer ' . $this->community_oauth
        );

        $response = \Utilities::SendRequest(COMMUNITIES_URL  . $communityId . "/threads/" . $threadId . "/messages/" . $messageId, $headers, false, null, "DELETE");

        $data = json_decode($response['body'], false);

        return $data;
    }
    public function DeleteMessageReply($communityId, $threadId, $parentId, $messageId)
    {
        $headers = array(
            'Authorization: Bearer ' . $this->community_oauth
        );

        $response = \Utilities::SendRequest(COMMUNITIES_URL  . $communityId . "/threads/" . $threadId . "/messages/" . $parentId . "/replies/" . $messageId, $headers, false, null, "DELETE");

        $data = json_decode($response['body'], false);

        return $data;
    }
    public function LeaveCommunity($communityId)
    {
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->community_oauth
        );

        //this method needs an empty json object... not sure why Sony did that
        $response = \Utilities::SendRequest(COMMUNITIES_URL  . $communityId . "/members", $headers, false, null, "DELETE", "{}");

        $data = json_decode($response['body'], false);

        return $data;
    }
    public function JoinCommunity($communityId)
    {
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->community_oauth
        );

        $body = array(
            'onlineIds' => array ()
        );

        //don't know why this isnt a PUT request but whatever, it's literally just like inviting but without any onlineIds passed.
        $response = \Utilities::SendRequest(COMMUNITIES_URL  . $communityId . "/members", $headers, false, null, "POST", json_encode($body));

        $data = json_decode($response['body'], false);

        return $data;
    }
    public function KickMembers($communityId, $onlineIds)
    {
        //for onlineids it requires array
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->community_oauth
        );

        $body = array(
            'onlineIds' => $onlineIds
        );

        $response = \Utilities::SendRequest(COMMUNITIES_URL  . $communityId . "/members?role=kicked", $headers, false, null, "DELETE", json_encode($body));

        $data = json_decode($response['body'], false);

        return $data;
    }

    public function ModifyNotification($communityId, $replies = FALSE, $wall = FALSE)
    {
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->community_oauth
        );

        $body = array(
            'repliesNotification' => $replies,
            'wallNotification' => $wall
        );

        $response = \Utilities::SendRequest(COMMUNITIES_URL  . $communityId . "/preferences", $headers, false, null, "POST", json_encode($body));

        $data = json_decode($response['body'], false);

        return $data;
    }

    public function GetUserRole($communityId, $onlineIds)
    {

        //for onlineids it requires array
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->community_oauth
        );

        $body = array(
            'onlineIds' => $onlineIds
        );

        $response = \Utilities::SendRequest(COMMUNITIES_URL  . $communityId . "/members", $headers, false, null, "POST", json_encode($body));

        $data = json_decode($response['body'], false);

        return $data;
    }

    public function PromoteMember($communityId, $onlineIds, $promotion = 1)
    {
        // 1 == Moderator (Promote)
        // 0 == Member (Demote)

        if($promotion)
        {
            $role = "moderator";
        }
        else
        {
            $role = "member";
        }

        //for onlineids it requires array
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->community_oauth
        );

        $body = array(
            'onlineIds' => $onlineIds
        );

        $response = \Utilities::SendRequest(COMMUNITIES_URL  . $communityId . "/members?role=" . $role, $headers, false, null, "PUT", json_encode($body));

        $data = json_decode($response['body'], false);

        return $data;
    }

}
