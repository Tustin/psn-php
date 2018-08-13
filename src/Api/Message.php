<?php

namespace PlayStation\Api;

use PlayStation\Client;
use PlayStation\Api\User;

class Message extends AbstractApi 
{
    private $message;
    private $messageThread;

    public function __construct(Client $client, object $message, MessageThread $messageThread)
    {
        parent::__construct($client);

        $this->message = $message;
        $this->messageThread = $messageThread;
    }

    public function sender() : User
    {
        return $this->client->user($this->message->sender->onlineId);
    }

    public function thread() : MessageThread
    {
        return $this->messageThread;
    }

    public function getBody() : string
    {
        return $this->message->messageDetail->body;
    }

    public function getDate() : \DateTime
    {
        return new \DateTime($this->message->postDate);
    }

}