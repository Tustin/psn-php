<?php
namespace PSN;

class Auth
{
    //Private properties
    private $oauth;

    private $last_error;

    private $npsso;

    private $grant_code;

    private $refresh_token;

    //POST data for the initial request (for the NPSSO Id)
    private $login_request = [
        "authentication_type" => "password",
        "username" => null,
        "password" => null,
        "client_id" => "71a7beb8-f21a-47d9-a604-2e71bee24fe0",
    ];
    //POST data for the oauth token
    private $oauth_request = [
        "app_context" => "inapp_ios",
        "client_id" => "b7cbf451-6bb6-4a5a-8913-71e61f462787",
        "client_secret" => "zsISsjmCx85zgCJg",
        "code" => null,
        "duid" => "0000000d000400808F4B3AA3301B4945B2E3636E38C0DDFC",
        "grant_type" => "authorization_code",
        "scope" => "capone:report_submission,psn:sceapp,user:account.get,user:account.settings.privacy.get,user:account.settings.privacy.update,user:account.realName.get,user:account.realName.update,kamaji:get_account_hash,kamaji:ugc:distributor,oauth:manage_device_usercodes"
    ];
    //GET data for the X-NP-GRANT-CODE
    private $code_request = [
        "state" => "06d7AuZpOmJAwYYOWmVU63OMY",
        "duid" => "0000000d000400808F4B3AA3301B4945B2E3636E38C0DDFC",
        "app_context" => "inapp_ios",
        "client_id" => "b7cbf451-6bb6-4a5a-8913-71e61f462787",
        "scope" => "capone:report_submission,psn:sceapp,user:account.get,user:account.settings.privacy.get,user:account.settings.privacy.update,user:account.realName.get,user:account.realName.update,kamaji:get_account_hash,kamaji:ugc:distributor,oauth:manage_device_usercodes",
        "response_type" => "code"
    ];
    //POST data for the refresh oauth token (allows user to stay signed in without entering info again (assuming you've kept the refresh token))
    private static $refresh_oauth_request = [
        "app_context" => "inapp_ios",
        "client_id" => "b7cbf451-6bb6-4a5a-8913-71e61f462787",
        "client_secret" => "zsISsjmCx85zgCJg",
        "refresh_token" => null,
        "duid" => "0000000d000400808F4B3AA3301B4945B2E3636E38C0DDFC",
        "grant_type" => "refresh_token",
        "scope" => "capone:report_submission,psn:sceapp,user:account.get,user:account.settings.privacy.get,user:account.settings.privacy.update,user:account.realName.get,user:account.realName.update,kamaji:get_account_hash,kamaji:ugc:distributor,oauth:manage_device_usercodes"
    ];
    
    public function __construct($Email, $Password)
    {
        //Store login data in the array
        $this->login_request['username'] = $Email;
        $this->login_request['password'] = $Password;

        //Throws a PSNAuthException if any form of authentication has failed
        if (!$this->GrabNPSSO() || !$this->GrabCode() || !$this->GrabOAuth())
        {
            throw new PSNAuthException($this->last_error);
        }
    }

    //Fetches the last error caught by the class
    public function GetLastError()
    {
        return $this->last_error;
    }

    //Grabs X-NP-GRANT-CODE
    public function GrabCode()
    {
        $response = \Utilities::SendRequest(CODE_URL, null, true, $this->npsso, "GET", http_build_query($this->code_request));

        $http_code = \Utilities::get_response_code($response["headers"]);
        
        //Needs custom error handling due to the type of response (or lack thereof)
        //HTTP code that will be given due to too many requests from a single IP
        if ($http_code == 503) {
            $error = [
                'error' => 'service_unavailable',
                'error_description' => 'Service unavailable. Possible IP block.',
                'error_code' => 20
            ];
            $this->last_error = json_encode($error);
            return false;
        }
        //If the grant code does not exist in the response header
        if (!$response["headers"][0]["X-NP-GRANT-CODE"]) {
            $error = [
                'error' => 'invalid_np_grant',
                'error_description' => 'Failed to obtain X-NP-GRANT-CODE',
                'error_code' => 20
            ];
            $this->last_error = json_encode($error);
            return false;
        }

        $this->grant_code = $response["headers"][0]["X-NP-GRANT-CODE"];

        return true;
    }

    //Grabs an OAuth Token
    public function GrabOAuth()
    {
        $this->oauth_request['code'] = $this->grant_code;

        $response = \Utilities::SendRequest(OAUTH_URL, null, false, null, "POST", http_build_query($this->oauth_request));

        $data = json_decode($response["body"], false);

        if (property_exists($data, "error")){
            $this->last_error = $response["body"];
            return false;
        }

        $this->oauth = $data->access_token;
        $this->refresh_token = $data->refresh_token;

        return true;
    }

    //Saves NPSSO Id to a cookie (not really necessary)
    public function SaveNPSSO()
    {
        setcookie("npsso", $this->npsso, strtotime("+1 month"), "/", null, null, true);
    }

    //This function will generate new tokens without requiring the user to login again, so long as you kept their refresh token.
    //We want this to be a static function so you can use it without creating a new instance of Auth just for new tokens.
    //Returns FALSE on error
    //Otherwise the new tokens will be returned as an array, just like GetTokens().
    public static function GrabNewTokens($RefreshToken)
    {
        if (!isset($RefreshToken))
            return false;

        Auth::$refresh_oauth_request["refresh_token"] = $RefreshToken;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, OAUTH_URL);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(Auth::$refresh_oauth_request));

        $output = curl_exec($ch);

        curl_close($ch);

        $data = json_decode($output, false);

        if (property_exists($data, "error")){
            return false;
        }

        return [
            "oauth" => $data->access_token,
            "refresh" => $data->refresh_token
        ];
    }

    //Grabs the NPSSO Id
    public function GrabNPSSO()
    {
        $response = \Utilities::SendRequest(SSO_URL, null, false, false, "POST", http_build_query($this->login_request));
        $data = json_decode($response["body"], false);

        if (property_exists($data, "error")){
            $this->last_error = $response["body"];
            return false;
        }

        $this->npsso = $data->npsso;
        $this->SaveNPSSO();
        return true;
    }

    //Returns the current OAuth tokens (required for other classes)
    //oauth => used for requests to the API
    //refresh => used for generating a new oauth token without logging in each time
    public function GetTokens()
    {
        return [
            "oauth" => $this->oauth,
            "refresh" => $this->refresh_token
        ];
    }
}