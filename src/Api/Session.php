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

    /**
     * Get Session information.
     *
     * @return object
     */
    public function info() : \stdClass
    {
        return $this->session;
    }
    
    /**
     * Get platform the Session is on.
     *
     * @return string
     */
    public function platform() : string 
    {
        return $this->session->platform;
    }

    /**
     * Get the ID of the Session.
     *
     * @return string
     */
    public function id() : string 
    {
        return $this->session->sessionId;
    }

    /**
     * Get the name of the Session.
     *
     * @return string
     */
    public function name() : string 
    {
        return $this->session->sessionName;
    }

    /**
     * Get maximum amount of users allowed in the Session.
     *
     * @return integer
     */
    public function maxUsers() : int
    {
        return $this->session->sessionMaxUser;
    }

    /**
     * Get DateTime of when the Session was created.
     *
     * @return \DateTime
     */
    public function creationDate() : \DateTime 
    {
        return new \DateTime($this->session->sessionCreateTimestamp);
    }

    /**
     * Get name of the game the Session is for.
     *
     * @return string|null
     */
    public function gameName() : ?string
    {
        if ($this->titleType() & SessionType::Unknown) return null; 

        return $this->session->npTitleDetail->npTitleName;
    }

    /**
     * Get title ID of the game the Session is for.
     *
     * @return string|null
     */
    public function gameTitleId() : ?string
    {
        if ($this->titleType() & SessionType::Unknown) return null; 

        return $this->session->npTitleDetail->npTitleId;
    }

    /**
     * Get icon URL of the game the Session is for.
     *
     * @return string|null
     */
    public function gameIconUrl() : ?string
    {
        if ($this->titleType() & SessionType::Unknown) return null; 

        return $this->session->npTitleDetail->npTitleIconUrl;
    }

    /**
     * Get Users in the Session.
     *
     * @return array|null Array of Api\User.
     */
    public function members() : array
    {
        $members = [];

        if (!isset($this->session->members) || $this->session->memberCount <= 0) return $members;

        foreach ($this->session->members as $member) {
            $members[] = new User($this->client, $member->onlineId);
        }

        return $members;
    }

    /**
     * Get SessionType of Session.
     *
     * @return integer SessionType flag.
     */
    public function titleType() : int
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