<?php

namespace Tustin\PlayStation\Models\Messages;

use Tustin\PlayStation\Enums\MessageType;
use Tustin\PlayStation\Models\Messages\Sendable;

class TextMessage extends Message implements Sendable
{
    public function message(): string
    {
        return $this->pluck('body');
    }

    /**
     * Gets the message type.
     */
    public function type(): MessageType
    {
        return MessageType::Text;
    }

    /**
     * Builds the message.
     * 
     * @return array<string, mixed>
     */
    public function build(): array
    {
        return [
            'messageType' => $this->type(),
            'body' => $this->message()
        ];
    }

    public function fetch(): object
    {
        throw new \BadMethodCallException();
    }
}
