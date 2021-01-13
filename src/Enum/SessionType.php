<?php

namespace Tustin\PlayStation\Enum;

use MyCLabs\Enum\Enum;

class SessionType extends Enum
{
    // Flags
    private const unknown = 1;
    private const game = 2;
    private const party = 4;
}