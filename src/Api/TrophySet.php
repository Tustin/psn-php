<?php

namespace PlayStation\Api;

use PlayStation\Client;

use PlayStation\Api\Game;
use PlayStation\Api\Trophy;
use PlayStation\Api\User;

class TrophySet extends AbstractApi 
{

    private $game;
    private $trophySet;

    public function __construct(Client $client, Game $game) 
    {
        parent::__construct($client);
        $this->game = $game;
    }

    public function name() : string 
    {
        return $this->info()->trophyTitleName;
    }

    public function detail() : string 
    {
        return $this->info()->trophyTitleDetail;
    }

    /**
     * Get the Trophy icon URL.
     *
     * @return string
     */
    public function iconUrl() : string 
    {
        return $this->info()->trophyTitleIconUrl;
    }

    /**
     * Get the Trophy platform (PS4, PSVita, PS3)
     *
     * @return string
     */
    public function platform() : string 
    {
        return $this->info()->trophyTitlePlatfrom;
    }

    public function info() : object
    {
        if ($this->trophySet === null) {
            $this->trophySet = $this->get(sprintf(Trophy::TROPHY_ENDPOINT. 'trophyTitles/%s/trophyGroups', $this->game->communicationId()), [
                'fields' => '@default,trophyTitleSmallIconUrl,trophyGroupSmallIconUrl',
                'iconSize' => 'm',
                'npLanguage' => 'en'
            ]);
        }

        return $this->trophySet;
    }


    /**
     * Gets the TrophyGroups for this TrophySet.
     *
     * @return array Array of Api\TrophyGroup
     */
    public function groups() : array
    {
        $returnGroups = [];

        $groups = $this->info()->trophyGroups;

        foreach ($groups as $group) {
            $returnGroups[] = new TrophyGroup($this->client, $group, $this);
        }

        return $returnGroups;
    }

    public function game() : Game
    {
        return $this->game;
    }
}