<?php

namespace Tustin\PlayStation\Model\Message;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Tustin\PlayStation\Model;
use Tustin\PlayStation\Model\User;
use Tustin\PlayStation\Enum\MessageType;
use Tustin\PlayStation\Model\MessageThread;

abstract class AbstractMessage extends Model
{
    public function __construct(protected Client $httpClient, private MessageThread $thread)
    {
        parent::__construct($thread->getHttpClient());
    }

    public static function fromObject(MessageThread $thread, object $data): self
    {
        return (new static($thread->getHttpClient(), $thread))->withCache($data);
    }

    /**
     * Gets the message type.
     */
    public abstract function type(): MessageType;

    /**
     * Gets the message id.
     */
    public function id(): string
    {
        return $this->pluck('messageUid');
    }

    /**
     * Gets the body of the message.
     */
    public function body(): string
    {
        return $this->pluck('body');
    }

    /**
     * Gets the date and time when the message was posted.
     */
    public function date(): \DateTime
    {
        return Carbon::parse($this->pluck('createdTimestamp'))->setTimezone('UTC');
    }

    /**
     * Returns the message thread that this message is in.
     */
    public function messageThread(): MessageThread
    {
        return $this->thread;
    }

    /**
     * Gets the message sender.
     */
    public function sender(): User
    {
        return new User(
            $this->getHttpClient(),
            $this->pluck('sender.accountId')
        );
    }

    /**
     * Creates a message based on the message type.
     */
    public static function create(MessageThread $thread, object $messageData): AbstractMessage
    {
        switch (MessageType::tryFrom($messageData->messageType)) {
            case MessageType::Audio:
                return AudioMessage::fromObject($thread, $messageData);
            case MessageType::Image:
                return ImageMessage::fromObject($thread, $messageData);
            case MessageType::Video:
                return VideoMessage::fromObject($thread, $messageData);
            case MessageType::Sticker:
                return StickerMessage::fromObject($thread, $messageData);
            default:
                // We'll just default to a text message because there are certain types of messages (new voice chat, etc) that are basically text messages.
                return TextMessage::fromObject($thread, $messageData);
        }
    }
}
