<?php

namespace PlayStation\Api;

use PlayStation\Client;
use PlayStation\Api\User;

class MessageThread extends AbstractApi 
{
    const MESSAGE_THREAD_ENDPONT    = 'https://us-gmsg.np.community.playstation.net/groupMessaging/v1/threads';

    private $messageThread;

    public function __construct(Client $client, object $messageThread)
    {
        parent::__construct($client);

        $this->messageThread = $messageThread;
    }

    public function leave()
    {
        return $this->delete(sprintf(self::MESSAGE_THREAD_ENDPONT . '/%s/users/me', $this->messageThread->threadId));
    }

    public function getInfo() : object
    {
        return $this->messageThread;
    }

    public function getMessages(int $count = 200) : array
    {
        $messages = [];

        $messageFields = $this->get(sprintf(self::MESSAGE_THREAD_ENDPONT . '/%s', $this->messageThread->threadId), [
            'fields' => 'threadMembers,threadNameDetail,threadThumbnailDetail,threadProperty,latestTakedownEventDetail,newArrivalEventDetail,threadEvents',
            'count' => $count
        ]);

        foreach ($messageFields->threadEvents as $message) {
            $messages[] = new Message($this->client, $message->messageEventDetail, $this);
        }

        return $messages;
    }

}