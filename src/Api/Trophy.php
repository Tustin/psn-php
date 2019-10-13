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

    /**
     * Gets the Trophy ID.
     *
     * @return integer
     */
    public function id() : int 
    {
        return $this->trophy->trophyId;
    }

    /**
     * Checks if Trophy is hidden.
     *
     * @return boolean
     */
    public function hidden() : bool 
    {
        return $this->trophy->trophyHidden;
    }

    /**
     * Gets the type of Trophy (bronze, silver, gold, platinum).
     *
     * @return string
     */
    public function type() : string
    {
        return $this->trophy->trophyType;
    }

    /**
     * Gets the name of the Trophy.
     *
     * @return string
     */
    public function name() : string
    {
        return $this->trophy->trophyName;
    }

    /**
     * Gets the Trophy's detail.
     *
     * @return string
     */
    public function detail() : string
    {
        return $this->trophy->trophyDetail;
    }

    /**
     * Gets the icon URL for the Trophy.
     *
     * @return string
     */
    public function iconUrl() : string
    {
        return $this->trophy->trophyIconUrl;
    }

    /**
     * Gets the total earned rate of the Trophy.
     *
     * @return float
     */
    public function earnedRate() : float
    {
        return floatval($this->trophy->trophyEarnedRate);
    }

    /**
     * Checks if User has earned the Trophy.
     *
     * @return boolean
     */
    public function earned() : bool
    {
        // fix someone can't get `earned`
        if ($this->comparing()) {
            if (property_exists($this->trophy, 'comparedUser')) {
                return $this->trophy->comparedUser->earned;
            }

            if (property_exists($this->trophy, 'fromUser')) {
                return $this->trophy->fromUser->earned;
            }
        }
        return false;
    }

    /**
     * Gets when the User earned the Trophy.
     *
     * @return \DateTime|null
     */
    public function earnedDate() : ?\DateTime
    {
        //fix sometime the `enrnedDate` is undefined
        if ($this->earned() && $this->comparing()) {
            try {

                if (property_exists($this->trophy, 'comparedUser') && property_exists($this->trophy->comparedUser, 'earnedDate')) {
                    return new \DateTime($this->trophy->comparedUser->earnedDate);
                }
                if (property_exists($this->trophy, 'fromUser') && property_exists($this->trophy->fromUser, 'earnedDate')) {
                    return new \DateTime($this->trophy->fromUser->earnedDate);
                }
            } catch (Exception $e) {
                return null;
            }
        }
        return null;
    }

    /**
     * Gets the TrophyGroup the Trophy is in.
     *
     * @return TrophyGroup
     */
    public function trophyGroup() : TrophyGroup
    {
        return $this->trophyGroup;
    }

    /**
     * Gets the Game the Trophy is for.
     *
     * @return Game
     */
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
    public static function calculateTrophies(object $trophyTypes) : int	
    {	
        return ($trophyTypes->bronze + $trophyTypes->silver + $trophyTypes->gold + $trophyTypes->platinum);	
    }

}