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

    public function earned() : bool
    {
        return $this->comparing() ?
        $this->trophy->comparedUser->earned :
        $this->trophy->fromUser->earned;
    }

    public function earnedDate() : ?\DateTime
    {
        if (!$this->earned()) return null;

        return new \DateTime(
            $this->comparing() ?
            $this->trophy->comparedUser->earnedDate :
            $this->trophy->fromUser->earnedDate
        );
    }

    public function trophyGroup() : TrophyGroup
    {
        return $this->trophyGroup;
    }

    public function game() : Game
    {
        return $this->trophyGroup()->game();
    }

    /**
     * Returns whether or not the TrophySet is for another user.
     *
     * @return boolean
     */
    public function comparing() : bool
    {
        return ($this->game()->user()->onlineId() !== null);
    }

    /**	
     * Calculate all the types of Trophies.	
     *	
     * @param object $trophyTypes Trophy type information.	
     * @return integer	
     */	
    private static function calculateTrophies(object $trophyTypes) : int	
    {	
        return ($trophyTypes->bronze + $trophyTypes->silver + $trophyTypes->gold + $trophyTypes->platinum);	
    }

}