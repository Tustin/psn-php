<?php

namespace Tustin\PlayStation\Models\Messages;

interface Sendable
{
    /**
     * Builds the message.
     */
    public function build(): array;
}
