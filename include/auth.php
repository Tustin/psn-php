<?php
namespace PSN\Auth;

define("OAUTH_URL", "https://auth.api.sonyentertainmentnetwork.com/2.0/oauth/token");
define("SSO_URL", "https://auth.api.sonyentertainmentnetwork.com/2.0/ssocookie");
define("CODE_URL", "https://auth.api.sonyentertainmentnetwork.com/2.0/oauth/authorize");
define("PROFILE_PIC_URL", "https://kfs.api.np.km.playstation.net/filestore/api/v1/users/me/profile/image");

class Auth
{
    //Private properties
    private $oauth;

    private $last_error;

    private $npsso = "npsso=";

    private $grant_code;

    //POST data for the initial request (for the NPSSO Id)
    private $login_request = [
        "authentication_type" => "password",
        "username" => null,
        "password" => null,
        "client_id" => "71a7beb8-f21a-47d9-a604-2e71bee24fe0",
    ];
    //POST data for the OAuth token
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
    
    public function __construct($Email, $Password)
    {
        //Store login data in the array
        $this->login_request['username'] = $Email;
        $this->login_request['password'] = $Password;

        //Tries fetching the NPSSO Id
        if (!$this->GrabNPSSO())
            die($this->GetLastError());

        //Tries fetching X-NP-GRANT-CODE
        if (!$this->GrabCode())
            die($this->GetLastError());

        //Tries fetching an OAuth token
        if (!$this->GrabOAuth())
            die($this->GetLastError());
    }
    //Function to return cURL headers in an array
    //http://stackoverflow.com/a/18682872
    private function get_headers_from_curl_response($headerContent)
    {
        $headers = array();

        $arrRequests = explode("\r\n\r\n", $headerContent);

        for ($index = 0; $index < count($arrRequests) -1; $index++) {

            foreach (explode("\r\n", $arrRequests[$index]) as $i => $line)
            {
                if ($i === 0)
                    $headers[$index]['http_code'] = $line;
                else
                {
                    list($key, $value) = explode(': ', $line);
                    $headers[$index][$key] = $value;
                }
            }
        }

        return $headers;
    }

    //Fetches the last error caught by the class
    public function GetLastError()
    {
        return $this->last_error;
    }
    //Grabs X-NP-GRANT-CODE
    public function GrabCode()
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, CODE_URL . "?" . http_build_query($this->code_request));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_COOKIE, $this->npsso);

        $output = curl_exec($ch);

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($output, 0, $header_size);
        $headers = $this->get_headers_from_curl_response($header);
        curl_close($ch);
        $http_code = explode(" ", $headers[0]["http_code"]);
        if ($http_code[1] == 503){
            $this->last_error = "Service unavailable. Possible IP block.";
            return false;
        }
        if (!$headers[0]["X-NP-GRANT-CODE"]){
            $this->last_error = "Failed to obtain X-NP-GRANT-CODE";
            return false;
        }

        $this->grant_code = $headers[0]["X-NP-GRANT-CODE"];

        return true;
    }

    //Grabs an OAuth Token
    public function GrabOAuth()
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, OAUTH_URL);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_COOKIE, $this->npsso);
        $this->oauth_request['code'] = $this->grant_code;
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->oauth_request));

        $output = curl_exec($ch);

        curl_close($ch);

        $data = json_decode($output, false);

        if (property_exists($data, "error")){
            $this->last_error = $data->error_description;
            return false;
        }

        $this->oauth = $data->access_token;

        return true;
    }

    //Grabs the NPSSO Id
    public function GrabNPSSO()
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, SSO_URL);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->login_request));

        $output = curl_exec($ch);

        curl_close($ch);

        $data = json_decode($output, false);

        if (property_exists($data, "error")){
            $this->last_error = $data->error_description;
            return false;
        }

        $this->npsso .= $data->npsso;
        return true;
    }

    //Returns the current OAuth token (required for other classes)
    public function GetAccessToken()
    {
        return $this->oauth;
    }
}