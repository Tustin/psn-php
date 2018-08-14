<?php

namespace PlayStation\Api;

use PlayStation\Client;

use PlayStation\Api\User;
use PlayStation\Api\Trophy;

class Game extends AbstractApi 
{

    const GAME_ENDPOINT = 'https://gamelist.api.playstation.com/v1/';

    private $titleId;
    private $game;

    /**
     * New instance of Api\Game.
     *
     * @param Client $client 
     * @param string|object $titleIdOrObject Title ID as a string or an object of title information.
     */
    public function __construct(Client $client, string $titleId)
    {
        parent::__construct($client);

        $this->titleId = $titleId;
    }

    public function titleId() : string
    {
        return $this->info()->titleId;
    }

    public function name() : string
    {
        return $this->info()->name;
    }
    
    public function imageUrl() : string 
    {
        return $this->info()->image;
    }

    public function communicationId() : string
    {
        return $this->info()->communcationId;
    }

    public function info() : object
    {
        if ($this->game === null) {
            // Kind of a hack here.
            // This endpoint doesn't give exactly the same information as the proper Game endpoint would,
            // But I wasn't able to find a way to get info from the Game endpoint with just a titleId.
            // It works, but I'd rather it be more consistent with the other endpoint.
            $game = $this->get(Trophy::TROPHY_ENDPOINT . 'apps/trophyTitles', [
                'npTitleIds' => $this->titleId,
                'fields' => '@default',
                'npLanguage' => 'en'
            ]);

            $this->game = new \stdClass();
            $this->game->titleId        = $game->apps[0]->npTitleId;
            $this->game->name           = $game->apps[0]->trophyTitles[0]->trophyTitleName;
            $this->game->image          = $game->apps[0]->trophyTitles[0]->trophyTitleIconUrls[1]->trophyTitleIconUrl;
            $this->game->communcationId = $game->apps[0]->trophyTitles[0]->npCommunicationId;
        }

        return $this->game;
    }


    /**
     * Gets the Users who have played this game.
     *
     * @return array Array of Api\User.
     */
    public function players() : array 
    {
        $returnPlayers = [];

        $players = $this->get(sprintf(GAME_ENDPOINT . 'titles/%s/players', $this->titleId));

        if ($players->size === 0) return $returnPlayers;

        foreach ($players->data as $player) {
            $returnPlayers[] = new User($this->client, $player->onlineId);
        }

        return $returnPlayers;
    }

    /**
     * Gets the TrophySet for this game.
     *
     * @return TrophySet
     */
    public function trophySet() : TrophySet
    {
        return new TrophySet($this->client, $this);
    }

}