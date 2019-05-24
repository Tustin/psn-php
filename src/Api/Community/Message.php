<?php

namespace PlayStation\Api\Community;

use PlayStation\Client;

use PlayStation\Api\AbstractApi;
use PlayStation\Api\User;

use PlayStation\Api\Community\Message;

class Message extends AbstractApi 
{
    private $message;
    private $thread;

    public function __construct(Client $client, object $message, Thread $thread) 
    {
        parent::__construct($client);

        $this->message = $message;
        $this->thread = $thread;
    }

    public function info() : \stdClass
    {
        return $this->message;
    }

    public function id() : string
    {
        return $this->info()->id;
    }

    public function message() : string
    {
        return $this->info()->message;
    }

    public function posted() : \DateTime
    {
        return new \DateTime($this->info()->creationTimestamp);
    }

    public function poster() : User
    {
        return new User($this->client, $this->info()->author->onlineId);
    }

    public function thread() : Thread
    {
        return $this->thread;
    }

    public function delete() : void
    {
        if (!$this->info()->permissions->delete) return;

        $this->delete(sprintf(self::COMMUNITY_ENDPOINT . 'communities/%s/threads/%s/messages/%s', $this->thread()->community()->id(), $this->thread()->id(), $this->id()));
    }
}