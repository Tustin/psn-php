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
        $response = \Utilities::SendRequest(MESSAGE_URL . '/' . $MessageGroupID . '/messages?fields=@default,messageGroup,stickerDetail,thumbnailDetail,body,takedownDetail,eventDetail,partyDetail&npLanguage=en-GB', $headers, false, null, "GET", null);


        $data = json_decode($response['body'], false);

        return $data;       
    }

    public function GetAudioAttachment($MessageGroupID, $MessageUid, $ContentKey = "image-data-0", $ContentType = "image/jpeg")
    {
        return GetAudioAttachment($MessageGroupID, $MessageUid, "voice-data-0", "audio/mpeg"); 
    }
    public function GetImageAttachment($MessageGroupID, $MessageUid, $ContentKey = "image-data-0", $ContentType = "image/jpeg")
    {
        return GetAudioAttachment($MessageGroupID, $MessageUid, "image-data-0", "image/jpeg");
    }
    public function GetAttachment($MessageGroupID, $MessageUid, $ContentKey, $ContentType)
    {
        $headers = array(
            'Authorization: Bearer ' . $this->oauth,
            'Content-Type: ' . $ContentType,
            'Content-Transfer-Encoding: binary'
        );
        $response = \Utilities::SendRequest(MESSAGE_URL . '/' . $MessageGroupID . '/messages/' . $MessageUid . '?contentKey=' . $ContentKey, $headers, false, null, "GET", null);


        $data = base64_encode($response['body']);

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
            "refresh" => $this->refresh_token
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

    public function MessageAttachment($PSN, $image)
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
                "body" => '',
                "messageKind" => 3
            )
        );

        // If $image is a url or file path, we'll grab the content, else assume we're parsing raw image data and send that instead.
        if (file_exists($image) || filter_var($image, FILTER_VALIDATE_URL) == true) {
            $imageContent = file_get_contents($image);
            $imageLength  = strlen($imageContent);
        } else {
            $imageContent = $image;
            $imageLength  = strlen($imageContent);
        }

        // var_dump($imageContent, $imageLength);
        // exit;

        //This formatting is bad but if you try to change it, the request will fail.
        $message = '--gc0p4Jq0M2Yt08jU534c0p
Content-Type: application/json; charset=utf-8
Content-Description: message

' . json_encode($json_body) . '
--gc0p4Jq0M2Yt08jU534c0p
Content-Type: image/jpeg
Content-Disposition: attachment
Content-Description: image-data-0
Content-Transfer-Encoding: binary
Content-Length: ' . $imageLength . '

' . $imageContent . '
--gc0p4Jq0M2Yt08jU534c0p--';

        $response = \Utilities::SendRequest(MESSAGE_URL, $headers, false, null, "POST", $message);

        $data = json_decode($response['body'], false);

        return $data;
    }
}