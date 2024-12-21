<?php

namespace Tustin\PlayStation\Enums;


enum TrophyServiceName: string
{
    /**
     * For PS3, PS4, Vita
     */
    case Trophy = 'trophy';

    /**
     * For PS5, PC
     */
    case Trophy2 = 'trophy2';
}
