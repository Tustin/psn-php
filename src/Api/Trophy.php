<?php

namespace PlayStation\Api;

use PlayStation\Client;
use PlayStation\Api\User;

class Trophy extends AbstractApi 
{

    public const TROPHY_ENDPOINT    = 'https://us-tpy.np.community.playstation.net/trophy/v1/';

    private $user;

    public function __construct(Client $client, User $user) 
    {
        parent::__construct($client);

        $this->user = $user;
    }

    public function getAll(int $limit = 36) 
    {
        $data = [
            'fields' => '@default',
            'npLanguage' => 'en',
            'iconSize' => 'm',
            'platform' => 'PS3,PSVITA,PS4',
            'offset' => 0,
            'limit' => $limit
        ];

        if ($this->user->getOnlineId() != null) {
            $data['comparedUser'] = $this->user->getOnlineId();
        }
        return $this->get(self::TROPHY_ENDPOINT . 'trophyTitles', $data);
    }

    public function deleteTrophy(string $gameContentId) 
    {
        if ($this->user->getOnlineId() != null) return;

        return $this->delete(sprintf(self::TROPHY_ENDPOINT . '%s/trophyTitles/%s', $this->client->getOnlineId(), $gameContentId))
    }
}