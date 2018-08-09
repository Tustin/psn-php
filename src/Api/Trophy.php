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

    public function getInfo() : object
    {
        return $this->trophy;
    }

    public function getName() : string 
    {
        return $this->trophy->trophyTitleName;
    }

    public function getDetail() : string 
    {
        return $this->trophy->trophyTitleDetail;
    }

    public function getIconUrl() : string 
    {
        return $this->trophy->trophyTitleIconUrl;
    }

    public function getPlatform() : string 
    {
        return $this->trophy->trophyTitlePlatfrom;
    }

    public function getNpCommunicationId() : string 
    {
        return $this->trophy->npCommunicationId;
    }

    public function getLastUpdateDate() : \DateTime 
    {
        return new \DateTime($this->trophy->lastUpdateDate);
    }

    public function getTotalEarnedTrophies() : int
    {
        return $this->calculateTrophies(
            ($this->isCompared) ?
            $this->trophy->comparedUser->earnedTrophies : 
            $this->trophy->fromUser->earnedTrophies
        );
    }
    
    public function getTotalGameTrophies() : int
    {
        return $this->calculateTrophies($this->trophy->definedTrophies);
    }


    public function deleteTrophySet() : void
    {
        if ($this->user->getOnlineId() != null) return;

        $this->delete(sprintf(self::TROPHY_ENDPOINT . '%s/trophyTitles/%s', $this->client->getOnlineId(), $this->getNpCommunicationId()));
    }


    private function calculateTrophies(object $trophyTypes) : int
    {
        return ($trophyTypes->bronze + $trophyTypes->silver + $trophyTypes->gold + $trophyTypes->platinum);
    }
}