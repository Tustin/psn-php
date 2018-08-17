<?php

namespace PlayStation\Api;

use PlayStation\Client;
use PlayStation\Api\User;

class TrophyGroup extends AbstractApi 
{

    private $group;
    private $game;

    public function __construct(Client $client, object $group, Game $game) 
    {
        parent::__construct($client);

        $this->group = $group;
        $this->game = $game;
    }

    public function id()
    {
        return $this->group->trophyGroupId;
    }

    public function name() : string
    {
        return $this->group->trophyGroupName;
    }

    public function detail() : string
    {
        return $this->group->trophyGroupDetail;
    }

    public function iconUrl() : string
    {
        return $this->group->trophyGroupIconUrl;
    }

    public function trophyCount() : int 
    {
        return Trophy::calculateTrophies($this->group->definedTrophies);
    }

    public function progress() : int
    {
        return $this->comparing() ?
        $this->group->comparedUser->progress :
        $this->group->fromUser->progress;
    }

    public function lastEarnedDate() : \DateTime
    {
        return new \DateTime(
            $this->comparing() ?
            $this->group->comparedUser->lastUpdateDate :
            $this->group->fromUser->lastUpdateDate
        );
    }


    /**
     * Get last TrophyGroup earned DateTIme.
     *
     * @return \DateTime
     */
    public function lastUpdateDate() : \DateTime 
    {
        return new \DateTime($this->group->lastUpdateDate);
    }

    public function trophies() : array 
    {
        $returnTrophies = [];

        $data = [
            'fields' => '@default,trophyRare,trophyEarnedRate,hasTrophyGroups,trophySmallIconUrl',
            'iconSize' => 'm',
            'visibleType' => 1,
            'npLanguage' => 'en'
        ];

        if ($this->comparing()) {
            $data['comparedUser'] = $this->game()->user()->onlineId();
        }

        $trophies = $this->get(sprintf(Trophy::TROPHY_ENDPOINT . 'trophyTitles/%s/trophyGroups/%s/trophies', $this->game()->communicationId(), $this->id()), $data);

        foreach ($trophies->trophies as $trophy) {
            $returnTrophies[] = new Trophy($this->client, $trophy, $this);
        }

        return $returnTrophies;
    }

    public function game() : Game
    {
        return $this->game;
    }

    /**
     * Returns whether or not the Game is for another user.
     *
     * @return boolean
     */
    public function comparing() : bool
    {
        return $this->game()->comparing();
    }
}