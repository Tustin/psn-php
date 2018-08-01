<?php
namespace PSN;

class Trophy
{
    private $oauth;
    private $refresh_token;

    public function __construct($tokens)
    {
        $this->oauth = $tokens["oauth"];
        $this->refresh_token = $tokens["refresh"];
    }

    //Returns all the trophies that the current logged in user has earned
    public function GetMyTrophies($Limit = 36)
    {
        $headers = array(
            'Authorization: Bearer ' . $this->oauth,
        );

        $response = \Utilities::SendRequest(TROPHY_URL . "trophyTitles?fields=@default&npLanguage=en&iconSize=m&platform=PS3,PSVITA,PS4&offset=0&limit=" . $Limit, $headers, false, null, "GET", null);

        $data = json_decode($response['body'], false);

        return $data;
    }

    //Returns all trophies from a game based on it's game ID (ex. NPWR07466_00)
    public function GetGameTrophies($GameID)
    {
        $headers = array(
            'Authorization: Bearer ' . $this->oauth,
        );

        $response = \Utilities::SendRequest(TROPHY_URL . "trophyTitles/" . $GameID . "/trophyGroups/all/trophies?fields=@default,trophyRare,trophyEarnedRate&npLanguage=en&sortKey=trophyId&iconSize=m", $headers, false, null, "GET", null);

        $data = json_decode($response['body'], false);

        return $data;
    }

    public function GetGameTrophiesByGroup($GameID, $GroupID)
    {
        $headers = array(
            'Authorization: Bearer ' . $this->oauth,
        );

        $response = \Utilities::SendRequest(TROPHY_URL . "trophyTitles/" . $GameID . "/trophyGroups/" . $GroupID . "/trophies?fields=@default,trophyRare,trophyEarnedRate&npLanguage=en&sortKey=trophyId&iconSize=m", $headers, false, null, "GET", null);

        $data = json_decode($response['body'], false);

        return $data;
    }

    public function GetGameTrophyGroups($GameID)
    {
        $headers = array(
            'Authorization: Bearer ' . $this->oauth,
        );

        $response = \Utilities::SendRequest(TROPHY_URL . "trophyTitles/" . $GameID . "/trophyGroups/?npLanguage=en", $headers, false, null, "GET", null);

        $data = json_decode($response['body'], false);

        return $data;
    }

    //Fetch trophy titles using game id from presences (e.g. CUSA03952_00), usefull for connecting trophies and online status
    public function FetchTrophyTitles($GameID)
    {
        $headers = array(
            'Authorization: Bearer ' . $this->oauth,
        );

        $response = \Utilities::SendRequest(TROPHY_URL . "apps/trophyTitles?npLanguage=en&npTitleIds=$GameID", $headers, false, null, "GET", null);

        $data = json_decode($response['body'], false);

        return $data;
    }
}
