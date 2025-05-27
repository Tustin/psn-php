<?php

namespace Tustin\PlayStation\Model\Messages;

interface Sendable
{
    /**
     * Builds the message.
     */
    public function build(): array;
}
