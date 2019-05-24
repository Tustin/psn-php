<?php

namespace PlayStation\Api;

use PlayStation\Client;
use PlayStation\Api\User;

class Comment extends AbstractApi 
{
    private $comment;
    private $story;

    public function __construct(Client $client, object $comment, Story $story) 
    {
        parent::__construct($client);
        $this->comment = $comment;

        $this->story = $story;        
    }

    /**
     * Gets the Story this Comment is for.
     *
     * @return Story
     */
    public function story() : Story 
    {
        return $this->story;
    }

    /**
     * Gets the info for the Comment.
     *
     * @return object
     */
    public function info() : \stdClass
    {
        return $this->comment;
    }

    /**
     * Gets the User who made the Comment.
     *
     * @return User
     */
    public function user() : User
    {
        return new User($this->client, $this->info()->onlineId);
    }

    /**
     * Gets the Comments message.
     *
     * @return string
     */
    public function comment() : string
    {
        return $this->info()->commentString;
    }

    /**
     * Gets the Comment ID.
     *
     * @return string
     */
    public function commentId() : string
    {
        return $this->info()->commentId;
    }

    /**
     * Gets the DateTime of the Comment.
     *
     * @return \DateTime
     */
    public function postDate() : \DateTime
    {
        return new \DateTime($this->info()->date);
    }

}