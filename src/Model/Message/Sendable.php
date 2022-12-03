<?php

namespace Tustin\PlayStation\Model\Message;

interface Sendable
{
    public function build(): array;
}
