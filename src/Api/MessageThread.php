<?php

namespace PlayStation\Api;

use PlayStation\Client;
use PlayStation\Api\User;

class MessageThread extends AbstractApi 
{
    const MESSAGE_THREAD_ENDPOINT    = 'https://us-gmsg.np.community.playstation.net/groupMessaging/v1/';

    private $messageThread;
    private $messageThreadId;

    public function __construct(Client $client, $messageThreadOrId)
    {
        parent::__construct($client);

        $type = gettype($messageThreadOrId);
        if ($type === 'string') {
            $this->messageThreadId = $messageThreadOrId;
        } else if ($type === 'object') {
            $this->messageThread = $messageThread;
            $this->messageThreadId =  $this->messageThread->threadId;
        }
    }

    /**
     * Leave the MessageThread.
     *
     * @return void
     */
    public function leave() : void
    {
        $this->delete(sprintf(self::MESSAGE_THREAD_ENDPOINT . 'threads/%s/users/me', $this->messageThreadId));
    }

    /**
     * Get members in the MessageThread.
     *
     * @return array Array of Api\User.
     */
    public function members() : array
    {
        $members = [];

        if (!isset($this->getInfo()->threadMembers) || $this->getInfo()->threadMembers <= 0) return null;

        foreach ($this->getInfo()->threadMembers as $member) {
            $members[] = new User($this->client, $member->onlineId);
        }

        return $members;
    }

    /**
     * Get the MessageThread info.
     *
     * @param integer $count Amount of messages to return.
     * @param boolean $force Force an update.
     * @return object
     */
    public function getInfo(int $count = 1, bool $force = false) : object
    {
        if ($this->messageThread === null || $force) {
            $this->messageThread = $this->get(sprintf(self::MESSAGE_THREAD_ENDPOINT . 'threads/%s', $this->messageThreadId), [
                'fields' => 'threadMembers,threadNameDetail,threadThumbnailDetail,threadProperty,latestTakedownEventDetail,newArrivalEventDetail,threadEvents',
                'count' => $count,
            ]);
        }

        return $this->messageThread;
    }

    /**
     * Set the name of the MessageThread.
     *
     * @param string $name Name of the MessageThread.
     * @return void
     */
    public function setName(string $name) : void
    {
        $data = (object)[
            'threadNameDetail' => (object)[
                'threadName' => $name
            ]
        ];

        $this->putJson(sprintf(self::MESSAGE_THREAD_ENDPOINT . 'threads/%s/name', $this->messageThreadId), $data);
    }

    /**
     * Favorite the MessageThread.
     *
     * @return void
     */
    public function favorite() : void
    {
        $data = (object)[
            'favoriteDetail' => (object)[
                'favoriteFlag' => true
            ]
        ];

        $this->putJson(sprintf(self::MESSAGE_THREAD_ENDPOINT . 'users/me/threads/%s/favorites', $this->messageThreadId), $data);
    }

    /**
     * Unfavorite the MessageThread.
     *
     * @return void
     */
    public function unfavorite() : void
    {
        $data = (object)[
            'favoriteDetail' => (object)[
                'favoriteFlag' => false
            ]
        ];

        $this->putJson(sprintf(self::MESSAGE_THREAD_ENDPOINT . 'users/me/threads/%s/favorites', $this->messageThreadId), $data);
    }

    /**
     * Get the amount of members.
     *
     * @return integer
     */
    public function getMemberCount() : int 
    {
        return count($this->getInfo()->threadMembers);
    }

    /**
     * Get the MessageThread name.
     *
     * @return string
     */
    public function getThreadName() : string
    {
        return $this->getInfo()->threadNameDetail->threadName;
    }

    /**
     * Send a text message.
     *
     * @param string $message The message to send.
     * @return Message|null
     */
    public function sendMessage(string $message) : ?Message 
    {
        $data = (object)[
            'messageEventDetail' => (object)[
                'eventCategoryCode' => 1, 
                'messageDetail' => (object)[
                    'body' => $message
                ]
            ]
        ];

        $parameters = [
            [
                'name' => 'messageEventDetail',
                'contents' => json_encode($data, JSON_PRETTY_PRINT),
                'headers' => [
                    'Content-Type' => 'application/json; charset=utf-8'
                ]
            ]
        ];

        $response = $this->postMultiPart(sprintf(MessageThread::MESSAGE_THREAD_ENDPOINT . 'threads/%s/messages', $this->messageThreadId), $parameters);

        $messageFields = $this->getInfo(1, true);

        if (!isset($messageFields->threadEvents)) return null;

        $messageData = $messageFields->threadEvents[0];

        return new Message($this->client, $messageData->messageEventDetail, $this);
    }

    /**
     * Send an image message.
     *
     * @param string $imageContents Raw bytes of the image.
     * @return Message|null
     */
    public function sendImage(string $imageContents) : ?Message
    {
        $data = (object)[
            'messageEventDetail' => (object)[
                'eventCategoryCode' => 3, 
                'messageDetail' => (object)[
                    'body' => ''
                ]
            ]
        ];

        $parameters = 
        [
            [
                'name' => 'messageEventDetail',
                'contents' => json_encode($data, JSON_PRETTY_PRINT),
                'headers' => [
                    'Content-Type' => 'application/json; charset=utf-8'
                ]
            ],
            [
                'name' => 'imageData',
                'contents' => $imageContents,
                'headers' => [
                    'Content-Type' => 'image/png',
                    'Content-Transfer-Encoding' => 'binary',
                ]
            ]
        ];

        $response = $this->postMultiPart(sprintf(MessageThread::MESSAGE_THREAD_ENDPOINT . 'threads/%s/messages', $this->messageThreadId), $parameters);

        $messageFields = $this->getInfo(1, true);

        if (!isset($messageFields->threadEvents)) return null;

        $messageData = $messageFields->threadEvents[0];

        return new Message($this->client, $messageData->messageEventDetail, $this);
    }

    /**
     * Send an audio message.
     *
     * @param string $audioContents Raw bytes of the audio.
     * @param integer $audioLengthSeconds Length of the audio in seconds.
     * @return Message|null
     */
    public function sendAudio(string $audioContents, int $audioLengthSeconds) : ?Message
    {
        $data = (object)[
            'messageEventDetail' => (object)[
                'eventCategoryCode' => 1011, 
                'messageDetail' => (object)[
                    'body' => '',
                    'voiceDetail' => (object)[
                        'playbackTime' => $audioLengthSeconds
                    ]
                ]
            ]
        ];

        $parameters = 
        [
            [
                'name' => 'messageEventDetail',
                'contents' => json_encode($data, JSON_PRETTY_PRINT),
                'headers' => [
                    'Content-Type' => 'application/json; charset=utf-8'
                ]
            ],
            [
                'name' => 'voiceData',
                'contents' => $audioContents,
                'headers' => [
                    'Content-Type' => 'audio/3gpp',
                    'Content-Transfer-Encoding' => 'binary',
                ]
            ]
        ];

        $response = $this->postMultiPart(sprintf(MessageThread::MESSAGE_THREAD_ENDPOINT . 'threads/%s/messages', $this->messageThreadId), $parameters);

        $messageFields = $this->getInfo(1, true);

        if (!isset($messageFields->threadEvents)) return null;

        $messageData = $messageFields->threadEvents[0];

        return new Message($this->client, $messageData->messageEventDetail, $this);
    }

    /**
     * Get all the messages.
     *
     * @param integer $count Amount of messages to send.
     * @return array Array of Api\Message.
     */
    public function getMessages(int $count = 200) : array
    {
        $messages = [];

        $messageFields = $this->getInfo($count, true);

        foreach ($messageFields->threadEvents as $message) {
            $messages[] = new Message($this->client, $message->messageEventDetail, $this);
        }

        return $messages;
    }

}