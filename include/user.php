<?php
namespace PSN\Users;

define("ACTIVITY_URL", "https://activity.api.np.km.playstation.net/activity/api/v1/users/");

class User
{
    private $oauth;

    public function __construct($AccessToken)
    {
        $this->oauth = $AccessToken;
    }

    public function Me()
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, USERS_URL . "me/profile2?fields=npId,onlineId,avatarUrls,plus,aboutMe,languagesUsed,trophySummary(@default,progress,earnedTrophies),isOfficiallyVerified,personalDetail(@default,profilePictureUrls),personalDetailSharing,personalDetailSharingRequestMessageFlag,primaryOnlineStatus,presences(@titleInfo,hasBroadcastData),friendRelation,requestMessageFlag,blocking,mutualFriendsCount,following,followerCount,friendsCount,followingUsersCount&avatarSizes=m,xl&profilePictureSizes=m,xl&languagesUsedLanguageSet=set3&psVitaTitleIcon=circled&titleIconSize=s");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $this->oauth,
        ));

        $output = curl_exec($ch);

        curl_close($ch);

        $data = json_decode($output, false);

        return $data;
    }
    //Returns null if valid
    public function Add($PSN, $RequestMessage = "")
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, USERS_URL . $this->Me()->profile->onlineId . "/friendList/" . $PSN);


        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $headers = array(
            'Authorization: Bearer ' . $this->oauth,
            'Content-Type: application/json; charset=utf-8'
        );

        $request_message = !empty($RequestMessage) ? '{"requestMessage": "' . $RequestMessage . '"' : '{}';

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_message);

        $output = curl_exec($ch);

        curl_close($ch);

        $data = json_decode($output, false);

        return $data;
    }

    public function Block($PSN)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, USERS_URL . $this->Me()->profile->onlineId . "/blockList/" . $PSN . "?blockingUsersLimitType=1");


        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $headers = array(
            'Authorization: Bearer ' . $this->oauth,
            'Content-Type: application/json; charset=utf-8'
        );

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec($ch);

        curl_close($ch);

        $data = json_decode($output, false);

        return $data;
    }

    public function Unblock($PSN)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, USERS_URL . $this->Me()->profile->onlineId . "/blockList/" . $PSN . "?blockingUsersLimitType=1");


        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $headers = array(
            'Authorization: Bearer ' . $this->oauth,
            'Content-Type: application/json; charset=utf-8'
        );

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
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

    //Uploads an image for profile picture (still a WIP)
    public function UploadPicture($URL)
    {
        $bytes = file_get_contents($URL);
        $request = '--abcdefghijklmnopqrstuvwxyz
Content-Disposition: form-data; name="source"; filename=image.png
Content-Type: image/png

' . $bytes . '
--abcdefghijklmnopqrstuvwxyz--';

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, PROFILE_PIC_URL);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $headers = array(
            'Authorization: Bearer ' . $this->oauth,
            'Content-Type: multipart/form-data; boundary=abcdefghijklmnopqrstuvwxyz'
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);

        $output = curl_exec($ch);

        curl_close($ch);

        $data = json_decode($output, false);

        return $data;

    }
}
