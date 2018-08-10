<?php

namespace PlayStation\Api;

use PlayStation\Client;
use PlayStation\SessionType;

use PlayStation\Api\User;

class Session extends AbstractApi 
{
    public const SESSION_ENDPOINT = 'https://us-ivt.np.community.playstation.net/sessionInvitation/v1/users/%s/sessions';

    private $session;

    public function __construct(Client $client, object $session)
    {
        parent::__construct($client);

        $this->session = $session;
    }

    public function getInfo() : array
    {
        return $this->session;
    }

    public function getPlatform() : string 
    {
        return $this->session->platform;
    }

    public function getSessionId() : string 
    {
        return $this->session->sessionId;
    }

    public function getSessionName() : string 
    {
        return $this->session->sessionName;
    }

    public function getMaxUsers() : int
    {
        return $this->session->sessionMaxUser;
    }

    public function getCreationDate() : \DateTime 
    {
        return new \DateTime($this->session->sessionCreateTimestamp);
    }

    public function getGameName() : ?string
    {
        if ($this->getTitleType() & SessionType::Unknown) return null; 

        return $this->session->npTitleDetail->npTitleName;
    }

    public function getGameTitleId() : ?string
    {
        if ($this->getTitleType() & SessionType::Unknown) return null; 

        return $this->session->npTitleDetail->npTitleId;
    }

    public function getGameIconUrl() : ?string
    {
        if ($this->getTitleType() & SessionType::Unknown) return null; 

        return $this->session->npTitleDetail->npTitleIconUrl;
    }

    public function members() : ?array
    {
        $members = [];

        if (!isset($this->session->members) || $this->session->memberCount <= 0) return null;

        foreach ($this->session->members as $member) {
            $members[] = new User($this->client, $member->onlineId);
        }

        return $members;
    }

    public function getTitleType() : int
    {
        if (!isset($this->session->npTitleDetail)) return SessionType::Unknown;

        switch ($this->session->npTitleType) {
            case 'party':
            return SessionType::Party;
            case 'game':
            return sessionType::Game;
            default:
            return SessionType::Unknown;
        }
    }
}