<?php

namespace PlayStation\Api;

use PlayStation\Client;
use PlayStation\Api\User;

class Trophy extends AbstractApi 
{

    public const TROPHY_ENDPOINT    = 'https://us-tpy.np.community.playstation.net/trophy/v1/';

    private $trophy;
    private $user;

    private $isCompared;

    public function __construct(Client $client, object $trophy, User $user, bool $isCompared) 
    {
        parent::__construct($client);

        $this->trophy = $trophy;
        $this->user = $user;
        $this->isCompared = $isCompared;
    }

    /**
     * Get the Trophy information.
     *
     * @return object
     */
    public function getInfo() : object
    {
        return $this->trophy;
    }

    /**
     * Get the Trophy name.
     *
     * @return string
     */
    public function getName() : string 
    {
        return $this->trophy->trophyTitleName;
    }

    /**
     * Get the Trophy detail.
     *
     * @return string
     */
    public function getDetail() : string 
    {
        return $this->trophy->trophyTitleDetail;
    }

    /**
     * Get the Trophy icon URL.
     *
     * @return string
     */
    public function getIconUrl() : string 
    {
        return $this->trophy->trophyTitleIconUrl;
    }

    /**
     * Get the Trophy platform (PS4, PSVita, PS3)
     *
     * @return string
     */
    public function getPlatform() : string 
    {
        return $this->trophy->trophyTitlePlatfrom;
    }

    /**
     * Get the NP Communication ID for the Trophy (ex: XXXXYYYYY_ZZ)
     *
     * @return string
     */
    public function getNpCommunicationId() : string 
    {
        return $this->trophy->npCommunicationId;
    }

    /**
     * Get last Trophy earned DateTIme.
     *
     * @return \DateTime
     */
    public function getLastUpdateDate() : \DateTime 
    {
        return new \DateTime($this->trophy->lastUpdateDate);
    }

    /**
     * Get total amount of Trophies earned for this Trophy.
     *
     * @return integer
     */
    public function getTotalEarnedTrophies() : int
    {
        return $this->calculateTrophies(
            ($this->isCompared) ?
            $this->trophy->comparedUser->earnedTrophies : 
            $this->trophy->fromUser->earnedTrophies
        );
    }
    
    /**
     * Get amount of Trophies the current Trophy set has.
     *
     * @return integer
     */
    public function getTotalGameTrophies() : int
    {
        return $this->calculateTrophies($this->trophy->definedTrophies);
    }

    /**
     * Delete the current Trophy set.
     *
     * @return void
     */
    public function deleteTrophySet() : void
    {
        if ($this->user->getOnlineId() != null) return;

        $this->delete(sprintf(self::TROPHY_ENDPOINT . '%s/trophyTitles/%s', $this->client->getOnlineId(), $this->getNpCommunicationId()));
    }

    /**
     * Calculate all the types of Trophies.
     *
     * @param object $trophyTypes Trophy type information.
     * @return integer
     */
    private function calculateTrophies(object $trophyTypes) : int
    {
        return ($trophyTypes->bronze + $trophyTypes->silver + $trophyTypes->gold + $trophyTypes->platinum);
    }
}