<?php

namespace Tustin\PlayStation\Model\Message;

interface Sendable
{
    /**
     * Builds the message.
     */
    public function build(): array;
}
