<?php
//Definitions for API endpoints
define("ACTIVITY_URL",        "https://activity.api.np.km.playstation.net/activity/api/v1/users/");
define("OAUTH_URL",           "https://auth.api.sonyentertainmentnetwork.com/2.0/oauth/token");
define("SSO_URL",             "https://auth.api.sonyentertainmentnetwork.com/2.0/ssocookie");
define("CODE_URL",            "https://auth.api.sonyentertainmentnetwork.com/2.0/oauth/authorize");
define("USERS_URL",           "https://us-prof.np.community.playstation.net/userProfile/v1/users/");
define("MESSAGE_URL",         "https://us-gmsg.np.community.playstation.net/groupMessaging/v1/messageGroups");
define("MESSAGE_THREADS_URL", "https://us-gmsg.np.community.playstation.net/groupMessaging/v1/threads");
define("MESSAGE_USERS_URL",   "https://us-gmsg.np.community.playstation.net/groupMessaging/v1/users/");
define("TROPHY_URL",          "https://us-tpy.np.community.playstation.net/trophy/v1/");
define("COMMUNITIES_URL",     "https://communities.api.playstation.com/v1/communities/");

class Utilities 
{
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_OK = 200;
    const HTTP_NOT_FOUND = 404;
    //Function to return cURL headers in an array
    //http://stackoverflow.com/a/18682872
    public static function get_headers_from_curl_response($headerContent)
    {
        $headers = array();

        $arrRequests = explode("\r\n\r\n", $headerContent);

        for ($index = 0; $index < count($arrRequests) -1; $index++) 
        {

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

    public static function get_response_code($responseHeader)
    {
        $http_code = explode(" ", $responseHeader[0]["http_code"]);
        return $http_code[1];
    }
    //Sends a request (either POST or GET)
    //Returns an array of the response
    //['body'] => response body
    //['headers'] => response headers
    public static function SendRequest($URL, $Headers = null, $OutputHeader = false, $Cookie = null, $RequestMethod = "GET", $RequestArgs = null)
    {
        $ch = curl_init();
        $final = array();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if ($OutputHeader){
            curl_setopt($ch, CURLOPT_VERBOSE, 0);
            curl_setopt($ch, CURLOPT_HEADER, 1);
        }

        //Pretty dirty, but it allows us to send custom requests
        if (($RequestMethod == "POST" || $RequestMethod == "PUT" || $RequestMethod == "DELETE") && $RequestArgs != null)
            curl_setopt($ch, CURLOPT_POSTFIELDS, $RequestArgs);
        else if ($RequestArgs != null)
            $URL .= "?" . $RequestArgs;

        if ($RequestMethod != "POST" || $RequestMethod != "GET")
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $RequestMethod);

        curl_setopt($ch, CURLOPT_URL, $URL);

        if ($Cookie != null)
            curl_setopt($ch, CURLOPT_COOKIE, "npsso=" . $Cookie);

        if ($Headers != null)
            curl_setopt($ch, CURLOPT_HTTPHEADER, $Headers);

        $output = curl_exec($ch);
        $final['body'] = $output;

        if ($OutputHeader){
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header = substr($output, 0, $header_size);
            $headers = Utilities::get_headers_from_curl_response($header);
            $final['headers'] = $headers;
        }
        curl_close($ch);

        return $final;
    }
}
