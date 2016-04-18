<?php
namespace PSN\Trophies;

define("TROPHY_URL", "https://us-tpy.np.community.playstation.net/trophy/v1/");

class Trophy
{
    private $oauth;

    public function __construct($AccessToken)
    {
        $this->oauth = $AccessToken;
    }

    public function Get($Limit = 36)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, TROPHY_URL . "trophyTitles?fields=@default&npLanguage=en&iconSize=m&platform=PS3,PSVITA,PS4&offset=0&limit=" . $Limit);

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
    
    public function GetGameTrophies($GameID)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, TROPHY_URL . "trophyTitles/" . $GameID . "/trophyGroups/all/trophies?fields=@default,trophyRare,trophyEarnedRate&npLanguage=en&sortKey=trophyId&iconSize=m");

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
}