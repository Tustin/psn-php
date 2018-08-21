<?php

namespace PlayStation\Api;

use PlayStation\Client;
use PlayStation\Api\User;

class Community extends AbstractApi 
{

    public const COMMUNITY_ENDPOINT    = 'https://communities.api.playstation.com/v1/';

    private $community;

    public function __construct(Client $client, object $community) 
    {
        parent::__construct($client);

        $this->community = $community;
    }

    public function id() : string
    {
        return $this->community->id;
    }

    public function name() : string
    {
        return $this->community->name;
    }

    public function description() : string
    {
        return $this->community->description;
    }

    public function memberCount() : int 
    {
        return $this->community->members->size;
    }

}