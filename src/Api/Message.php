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

    /**
     * Get the sender of the message.
     *
     * @return User
     */
    public function sender() : User
    {
        return $this->client->user($this->message->sender->onlineId);
    }

    /**
     * Get the MessageThread the message is in.
     *
     * @return MessageThread
     */
    public function thread() : MessageThread
    {
        return $this->messageThread;
    }

    /**
     * Get the message body text.
     *
     * @return string
     */
    public function body() : string
    {
        return $this->message->messageDetail->body;
    }

    /**
     * Get the DateTime the message was sent.
     *
     * @return \DateTime
     */
    public function sendDate() : \DateTime
    {
        return new \DateTime($this->message->postDate);
    }
}