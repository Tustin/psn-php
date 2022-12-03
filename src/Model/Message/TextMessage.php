<?php

namespace Tustin\PlayStation\Model\Message;

use Tustin\PlayStation\Enum\MessageType;
use Tustin\PlayStation\Model\Message\Sendable;
use Tustin\PlayStation\Model\Message\AbstractMessage;

class TextMessage extends AbstractMessage implements Sendable
{
    public function __construct(private string $textMessage)
    {
    }

    public function type(): MessageType
    {
        return MessageType::Text;
    }

    public function build(): array
    {
        return [
            'messageType' => $this->type(),
            'body' => $this->textMessage
        ];
    }

    public function fetch(): object
    {
        throw new \BadMethodCallException();
    }
}
