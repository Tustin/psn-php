<?php

namespace Tustin\PlayStation\Model\Message;

use Carbon\Carbon;
use Tustin\PlayStation\Model\User;
use Tustin\PlayStation\Traits\Model;
use Tustin\PlayStation\Enum\MessageType;
use Tustin\PlayStation\Model\MessageThread;

abstract class AbstractMessage
{
    use Model;

    /**
     * The message thread this message is in.
     *
     * @var MessageThread
     */
    private $thread;

    public static function fromObject(MessageThread $thread, object $messageData)
    {
        $instance = new static('');
        $instance->setCache($messageData);

        $instance->thread = $thread;

        return $instance;
    }

    /**
     * Gets the message type.
     *
     * @return MessageType
     */
    public abstract function type(): MessageType;

    /**
     * Gets the message id.
     *
     * @return string
     */
    public function id(): string
    {
        return $this->pluck('messageUid');
    }

    /**
     * Gets the body of the message.
     *
     * @return string
     */
    public function body(): string
    {
        return $this->pluck('body');
    }

    /**
     * Gets the date and time when the message was posted.
     *
     * @return Carbon
     */
    public function date(): Carbon
    {
        return Carbon::parse($this->pluck('createdTimestamp'))->setTimezone('UTC');
    }

    /**
     * Returns the message thread that this message is in.
     *
     * @return MessageThread
     */
    public function messageThread(): MessageThread
    {
        return $this->thread;
    }

    /**
     * Gets the message sender.
     *
     * @return User
     */
    public function sender(): User
    {
        return new User(
            $this->messageThread()->getHttpClient(),
            $this->pluck('sender.accountId')
        );
    }

    /**
     * Creates a message based on the message type.
     *
     * @param MessageThread $thread
     * @param object $messageData
     * @return AbstractMessage
     */
    public static function create(MessageThread $thread, object $messageData): AbstractMessage
    {
        switch (new MessageType($messageData->messageType)) {
            case MessageType::audio():
                return AudioMessage::fromObject($thread, $messageData);
            case MessageType::image():
                return ImageMessage::fromObject($thread, $messageData);
            case MessageType::video():
                return VideoMessage::fromObject($thread, $messageData);
            case MessageType::sticker():
                return StickerMessage::fromObject($thread, $messageData);
            default:
                // We'll just default to a text message because there are certain types of messages (new voice chat, etc) that are basically text messages.
                return TextMessage::fromObject($thread, $messageData);
        }
    }
}
