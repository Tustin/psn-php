<?php

namespace Tustin\PlayStation\Models\Messages;

use Carbon\Carbon;
use Tustin\PlayStation\ApiModel;
use Tustin\PlayStation\Models\User;
use Tustin\PlayStation\Enums\MessageType;
use Tustin\PlayStation\Models\MessageThread;

abstract class Message extends ApiModel
{
    /**
     * The message thread this message is in.
     */
    public MessageThread $thread;

    /**
     * Creates a message object with existing message data.
     */
    public static function fromObject(MessageThread $thread, object $messageData): Message
    {
        $instance = new static();
        $instance->setCache($messageData);

        $instance->thread = $thread;

        return $instance;
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
     * Gets the message thread that this message is in.
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
            $this->pluck('sender.accountId')
        );
    }

    /**
     * Creates a message object based on the message type.
     */
    public static function create(MessageThread $thread, object $messageData): Message
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

    public function fetch(): object
    {
        throw new \BadMethodCallException();
    }
}
