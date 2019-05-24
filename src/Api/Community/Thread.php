<?php

namespace PlayStation\Api\Community;

use PlayStation\Client;

use PlayStation\Api\AbstractApi;
use PlayStation\Api\User;

class Thread extends AbstractApi 
{
    private $thread;
    private $community;

    public function __construct(Client $client, object $thread, Community $community) 
    {
        parent::__construct($client);

        $this->thread = $thread;
        $this->community = $community;
    }

    public function info() : \stdClass
    {
        return $this->thread;
    }

    public function id() : string
    {
        return $this->info()->id;
    }

    public function type() : string
    {
        return $this->info()->type;
    }

    public function name() : string
    {
        return $this->info()->name;
    }

    public function messages(int $limit = 100) : array
    {
        $returnMessages = [];

        $messages = $this->get(sprintf(self::COMMUNITY_ENDPOINT . 'communities/%s/threads/%s', $this->community()->id(), $this->id()), [
            'limit' => $limit
        ]);

        if ($messages->size === 0) return $returnMessages;

        foreach ($messages->messages as $message) {
            $returnMessages[] = new Message($this->client, $message, $this);
        }

        return $returnMessages;
    }

    public function sendMessage(string $message) : ?Message
    {
        $response = $this->postJson(sprintf(self::COMMUNITY_ENDPOINT . 'communities/%s/threads/%s/messages', $this->community()->id(), $this->id()), [
            'message' => $message
        ]);

        return null;
    }

    public function community() : Community
    {
        return $this->community;
    }
}