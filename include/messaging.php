<?php
namespace PSN\Message;

define("MESSAGING_URL", "https://us-gmsg.np.community.playstation.net/groupMessaging/v1/messageGroups");

class Messaging
{
    public function Message($PSN, $Message)
    {

        $ch = curl_init();

        $test = '--gc0p4Jq0M2Yt08jU534c0p
Content-Type: application/json; charset=utf-8
Content-Description: message

{
  "to" : [
    "' . $PSN . '"
  ],
  "message" : {
    "fakeMessageUid" : 1234,
    "body" : "' . $Message . '",
    "messageKind" : 1
  }
}
--gc0p4Jq0M2Yt08jU534c0p--';

        curl_setopt($ch, CURLOPT_URL, MESSAGING_URL);
        //
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $headers = array(
            'Authorization: Bearer ' . $this->oauth,
            'Content-Type: multipart/mixed; boundary="gc0p4Jq0M2Yt08jU534c0p"',
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $test);

        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        $output = curl_exec($ch);

        curl_close($ch);

        $data = json_decode($output, false);

        return $output;
    }
    private function RandomPSN($length = 3)
    {
        return substr(str_shuffle(str_repeat("abcdefghijklmnopqrstuvwxyz", $length)), 0, $length);
    }
}