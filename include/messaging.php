<?php
namespace PSN;

class Messaging
{
    private $oauth;
    private $refresh_token;

    public function __construct($tokens)
    {
        $this->oauth = $tokens["oauth"];
        $this->refresh_token = $tokens["refresh"];
    }

    public function Get($MessageGroupID)
    {
        $headers = array(
            'Authorization: Bearer ' . $this->oauth,
            'Content-Type: application/json; charset=utf-8'
        );
        $response = \Utilities::SendRequest(MESSAGE_URL . '/' . $MessageGroupID . '/messages', $headers, false, null, "GET", null);


        $data = json_decode($response['body'], false);

        return $data;       
    }

    public function Remove($MessageGroupID)
    {
        $headers = array(
            'Authorization: Bearer ' . $this->oauth,
            'Content-Type: application/json; charset=utf-8'
        );

        $tokens = array(
            "oauth" => $this->oauth,
            "refresh" => $this->refresh
        );
        
        $_user = new \PSN\User($tokens);
        $_onlineId = $_user->Me()->profile->onlineId;

        $response = \Utilities::SendRequest(MESSAGE_URL . "/" . $MessageGroupID . "/users/" . $_onlineId, $headers, false, null, "DELETE", null);

        $data = json_decode($response['body'], false);

        return $data;
    }
    public function GetAll()
    {
        $headers = array(
            'Authorization: Bearer ' . $this->oauth,
            'Content-Type: application/json; charset=utf-8'
        );
        $tokens = array(
            "oauth" => $this->oauth,
            "refresh" => $this->refresh
        );
        $_user = new \PSN\User($tokens);
        $_onlineId = $_user->Me()->profile->onlineId;

        $response = \Utilities::SendRequest(MESSAGE_USERS_URL . $_onlineId ."/messageGroups?fields=@default,messageGroupDetail,totalUnseenMessages,totalMessages,myGroupDetail,newMessageDetail,takedownDetail&includeEmptyMessageGroup=true", $headers, false, null, "GET", null);

        $data = json_decode($response['body'], false);

        return $data;    
    }

    public function Message($PSN, $Message)
    {
        $headers = array(
            'Authorization: Bearer ' . $this->oauth,
            'Content-Type: multipart/mixed; boundary="gc0p4Jq0M2Yt08jU534c0p"',
        );
        $json_body = array(
            "to" => array(
                $PSN
            ),
            "message" => array(
                "fakeMessageUid" => 1234,
                "body" => $Message,
                "messageKind" => 1
            )
        );
        //This formatting is bad but if you try to change it, the request will fail.
        $message = '--gc0p4Jq0M2Yt08jU534c0p
Content-Type: application/json; charset=utf-8
Content-Description: message

' . json_encode($json_body) .'
--gc0p4Jq0M2Yt08jU534c0p--';

        $response = \Utilities::SendRequest(MESSAGE_URL, $headers, false, null, "POST", $message);

        $data = json_decode($response['body'], false);

        return $data;
    }
}