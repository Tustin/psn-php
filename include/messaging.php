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