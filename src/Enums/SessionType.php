<?php

namespace Tustin\PlayStation\Enums;

enum SessionType: int
{
        // Flags
    case Unknown = 1;
    case Game = 2;
    case Party = 4;
}
