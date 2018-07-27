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
    private $login_request = array(
        "authentication_type" => "password",
        "username" => null,
        "password" => null,
        "client_id" => "ebee17ac-99fd-487c-9b1e-18ef50c39ab5",
    );
    
    //POST data for the oauth token
    private $oauth_request = array(
        "app_context" => "inapp_ios",
        "client_id" => "ebee17ac-99fd-487c-9b1e-18ef50c39ab5",
        "client_secret" => "e4Ru_s*LrL4_B2BD",
        "code" => null,
        "duid" => "0000000d00040080027BC1C3FBB84112BFC9A4300A78E96A",
        "grant_type" => "authorization_code",
        "scope" => "kamaji:get_players_met kamaji:get_account_hash kamaji:activity_feed_submit_feed_story kamaji:activity_feed_internal_feed_submit_story kamaji:activity_feed_get_news_feed kamaji:communities kamaji:game_list kamaji:ugc:distributor oauth:manage_device_usercodes psn:sceapp user:account.profile.get user:account.attributes.validate user:account.settings.privacy.get kamaji:activity_feed_set_feed_privacy kamaji:satchel kamaji:satchel_delete user:account.profile.update"
    );

    //GET data for the X-NP-GRANT-CODE
    private $code_request = array(
        "state" => "06d7AuZpOmJAwYYOWmVU63OMY",
        "duid" => "0000000d00040080027BC1C3FBB84112BFC9A4300A78E96A",
        "app_context" => "inapp_ios",
        "client_id" => "ebee17ac-99fd-487c-9b1e-18ef50c39ab5",
        "scope" => "kamaji:get_players_met kamaji:get_account_hash kamaji:activity_feed_submit_feed_story kamaji:activity_feed_internal_feed_submit_story kamaji:activity_feed_get_news_feed kamaji:communities kamaji:game_list kamaji:ugc:distributor oauth:manage_device_usercodes psn:sceapp user:account.profile.get user:account.attributes.validate user:account.settings.privacy.get kamaji:activity_feed_set_feed_privacy kamaji:satchel kamaji:satchel_delete user:account.profile.update",
        "response_type" => "code"
    );

    //POST data for the refresh oauth token (allows user to stay signed in without entering info again (assuming you've kept the refresh token))
    private static $refresh_oauth_request = array(
        "app_context" => "inapp_ios",
        "client_id" => "ebee17ac-99fd-487c-9b1e-18ef50c39ab5",
        "client_secret" => "e4Ru_s*LrL4_B2BD",
        "refresh_token" => null,
        "duid" => "0000000d00040080027BC1C3FBB84112BFC9A4300A78E96A",
        "grant_type" => "refresh_token",
        "scope" => "kamaji:get_players_met kamaji:get_account_hash kamaji:activity_feed_submit_feed_story kamaji:activity_feed_internal_feed_submit_story kamaji:activity_feed_get_news_feed kamaji:communities kamaji:game_list kamaji:ugc:distributor oauth:manage_device_usercodes psn:sceapp user:account.profile.get user:account.attributes.validate user:account.settings.privacy.get kamaji:activity_feed_set_feed_privacy kamaji:satchel kamaji:satchel_delete user:account.profile.update"
    );
    
    //POST data for the 2FA request (for the NPSSO Id)
    private $two_factor_auth_request = array(
        "authentication_type" => "two_step",
        "ticket_uuid" => null,
        "code" => null,
        "client_id" => "ebee17ac-99fd-487c-9b1e-18ef50c39ab5",
    );

    public function __construct($email, $password, $ticket = "", $code = "")
    {
        //Store login data in the array
        $this->login_request['username'] = $email;
        $this->login_request['password'] = $password;
        $this->two_factor_auth_request['ticket_uuid'] = $ticket;
        $this->two_factor_auth_request['code'] = $code;

        //Throws a AuthException if any form of authentication has failed
        if (!$this->GrabNPSSO() || !$this->GrabCode() || !$this->GrabOAuth())
        {
            throw new AuthException($this->last_error);
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
            $error = array(
                'error' => 'service_unavailable',
                'error_description' => 'Service unavailable. Possible IP block.',
                'error_code' => 20
            );
            $this->last_error = json_encode($error);
            return false;
        }
        //If the grant code does not exist in the response header
        if (!$response["headers"][0]["X-NP-GRANT-CODE"]) {
            $error = array(
                'error' => 'invalid_np_grant',
                'error_description' => 'Failed to obtain X-NP-GRANT-CODE',
                'error_code' => 20
            );
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

    //This function will generate new tokens without requiring the user to login again, so long as you kept their refresh token.
    //We want this to be a static function so you can use it without creating a new instance of Auth just for new tokens.
    //Returns FALSE on error
    //Otherwise the new tokens will be returned as an array, just like GetTokens().
    public static function GrabNewTokens($refreshToken)
    {
        Auth::$refresh_oauth_request["refresh_token"] = $refreshToken;

        $response = \Utilities::SendRequest(OAUTH_URL, null, false, null, "POST", http_build_query(Auth::$refresh_oauth_request));

        $data = json_decode($response['body'], false);

        if (property_exists($data, "error")){
            throw new AuthException(sprintf('[%s]: %s', $data->error_code,
                                                        $data->error_description));
        }

        return array(
            "oauth" => $data->access_token,
            "refresh" => $data->refresh_token
        );
    }

    //Grabs the NPSSO Id
    public function GrabNPSSO()
    {
        if ($this->two_factor_auth_request['ticket_uuid'] && $this->two_factor_auth_request['code'])
        {
            $response = \Utilities::SendRequest(SSO_URL, null, false, false, "POST", http_build_query($this->two_factor_auth_request));
        }
        else
        {
            $response = \Utilities::SendRequest(SSO_URL, null, false, false, "POST", http_build_query($this->login_request));
        }
        $data = json_decode($response["body"], false);

        if (property_exists($data, "error")){
            $this->last_error = $response["body"];
            return false;
        }
        if (property_exists($data, "ticket_uuid")){
            $error = array(
                'error' => '2fa_code_required',
                'error_description' => '2FA Code Required',
                'ticket' => $data->ticket_uuid
            );
            $this->last_error = json_encode($error);
            return false;
        }

        $this->npsso = $data->npsso;
        return true;
    }

    //Returns the current OAuth tokens (required for other classes)
    //oauth => used for requests to the API
    //refresh => used for generating a new oauth token without logging in each time
    //npsso => required for the Communities class
    public function GetTokens()
    {
        return array(
            "oauth" => $this->oauth,
            "refresh" => $this->refresh_token,
            "npsso" => $this->npsso
        );
    }
}
