<?php

namespace PlayStation\Api;

use PlayStation\Client;
use PlayStation\MessageType;

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
     * Get the MessageThread info.
     *
     * @param integer $count Amount of messages to return.
     * @param boolean $force Force an update.
     * @return object
     */
    public function info(int $count = 1, bool $force = false) : \stdClass
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
     * Gets the MessageThread ID.
     *
     * @return string
     */
    public function messageThreadId() : string
    {
        return $this->messageThreadId;
    }

    /**
     * Get the amount of members.
     *
     * @return integer
     */
    public function memberCount() : int 
    {
        return count($this->info()->threadMembers);
    }

    /**
     * Get the MessageThread name.
     *
     * @return string
     */
    public function name() : string
    {
        return $this->info()->threadNameDetail->threadName;
    }

    /**
     * Gets the MessageThread thumbnail URL.
     *
     * @return string
     */
    public function thumbnailUrl() : string
    {
        return ($this->info()->threadThumbnailDetail->status == 2) ? 
        "" : 
        $this->info()->threadThumbnailDetail->resourcePath;
    }

    /**
     * Gets the last time the MessageThread was modified.
     *
     * @return \DateTime
     */
    public function modifiedDate() : \DateTime
    {
        return new \DateTime($this->info()->threadModifiedDate);
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

        if (!isset($this->info()->threadMembers) || $this->info()->threadMembers <= 0) return null;

        foreach ($this->info()->threadMembers as $member) {
            $members[] = new User($this->client, $member->onlineId);
        }

        return $members;
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
     * Send a text message.
     *
     * @param string $message The message to send.
     * @return Message|null
     */
    public function sendMessage(string $message) : ?Message 
    {
        $data = (object)[
            'messageEventDetail' => (object)[
                'eventCategoryCode' => MessageType::Text, 
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

        $messageFields = $this->info(1, true);

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
                'eventCategoryCode' => MessageType::Image, 
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

        $messageFields = $this->info(1, true);

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
                'eventCategoryCode' => MessageType::Audio, 
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

        $messageFields = $this->info(1, true);

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
    public function messages(int $count = 200) : array
    {
        $messages = [];

        $messageFields = $this->info($count, true);

        foreach ($messageFields->threadEvents as $message) {
            $messages[] = new Message($this->client, $message->messageEventDetail, $this);
        }

        return $messages;
    }

    /**
     * Set the MessageThread thumbnail
     *
     * @param string $imageContents Raw bytes of the image.
     * @return void
     */
    public function setThumbnail(string $imageContents) : void
    {
        $parameters = [
            [
                'name' => 'threadThumbnail',
                'contents' => $imageContents,
                'headers' => [
                    'Content-Type' => 'image/jpeg',
                    'Content-Transfer-Encoding' => 'binary',
                ]
            ]
        ];

        $this->putMultiPart(sprintf(MessageThread::MESSAGE_THREAD_ENDPOINT . 'threads/%s/thumbnail', $this->messageThreadId), $parameters);
    }

    /**
     * Removes the MessageThread thumbnail.
     *
     * @return void
     */
    public function removeThumbnail() : void
    {
        $this->delete(sprintf(MessageThread::MESSAGE_THREAD_ENDPOINT . 'threads/%s/thumbnail', $this->messageThreadId));
    }

}