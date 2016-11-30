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
            throw new PSNAuthException($response["body"]); 
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
        // Set Headers
        $headers = array(
            'Authorization: Bearer ' . $this->community_oauth
        );

        // Send Request
        $response = \Utilities::SendRequest(COMMUNITIES_URL  . $communityId . "/members?limit=" . $limit, $headers);

        $data = json_decode($response['body'], false);
                        
        return $data;
    }

    public function GetMyCommunities($limit = 100) 
    {
        // Set Headers
        $headers = array(
            'Authorization: Bearer ' . $this->community_oauth,
        );

        // Send Request
        $response = \Utilities::SendRequest(COMMUNITIES_URL ."?fields=backgroundImage%2CtilebackgroundImage%2Cdescription%2Cid%2Cmembers%2Cname%2CprofileImage%2Crole%2CunreadMessageCount%2Csessions%2Ctype%2Clanguage%2Ctimezone%2CtitleName%2C%20titleId%2CnameLastModifiedBy%2CdescriptionLastModifiedBy%2CgriefReportableItems%2CgameSessions%2Cparties%26includeFields%3DgameSessions%2Cparties&limit=" . $limit, $headers);

        $data = json_decode($response['body'], false);
                        
        return $data;
    }
}