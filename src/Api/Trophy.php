<?php

namespace PlayStation\Api;

use PlayStation\Client;
use PlayStation\Api\User;

class Trophy extends AbstractApi 
{

    public const TROPHY_ENDPOINT    = 'https://us-tpy.np.community.playstation.net/trophy/v1/';

    private $trophy;
    private $trophyGroup;

    public function __construct(Client $client, object $trophy, TrophyGroup $trophyGroup) 
    {
        parent::__construct($client);
        
        $this->trophy = $trophy;
        $this->trophyGroup = $trophyGroup;        
    }

    public function id() : int 
    {
        return $this->trophy->trophyId;
    }

    public function hidden() : bool 
    {
        return $this->trophy->trophyHidden;
    }

    public function type() : string
    {
        return $this->trophy->trophyType;
    }

    public function name() : string
    {
        return $this->trophy->trophyName;
    }

    public function detail() : string
    {
        return $this->trophy->trophyDetail;
    }

    public function iconUrl() : string
    {
        return $this->trophy->trophyIconUrl;
    }

    public function earnedRate() : float
    {
        return floatval($this->trophy->trophyEarnedRate);
    }

    public function trophyGroup() : TrophyGroup
    {
        return $this->trophyGroup;
    }

}