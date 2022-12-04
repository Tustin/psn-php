<?php

namespace Tustin\PlayStation\Enum;

enum SessionType: int
{
    // Flags
    case Unknown = 1;
    case Game = 2;
    case Party = 4;
}