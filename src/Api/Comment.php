<?php

namespace PlayStation\Api;

use PlayStation\Client;
use PlayStation\Api\User;

class Comment extends AbstractApi 
{

    public const TROPHY_ENDPOINT    = 'https://us-tpy.np.community.playstation.net/trophy/v1/';

    private $comment;
    private $story;

    public function __construct(Client $client, object $comment, Story $story) 
    {
        parent::__construct($client);
        $this->comment = $comment;

        $this->story = $story;        
    }

    public function story() : Story 
    {
        return $this->story;
    }

    public function info() : object
    {
        return $this->comment;
    }

    public function user() : User
    {
        return new User($this->client, $this->info()->onlineId);
    }

    public function comment() : string
    {
        return $this->info()->commentString;
    }

    public function commentId() : string
    {
        return $this->info()->commentId;
    }

    public function postDate() : \DateTime
    {
        return new \DateTime($this->info()->date);
    }

}