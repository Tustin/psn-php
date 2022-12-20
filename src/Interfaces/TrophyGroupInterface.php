<?php

namespace Tustin\PlayStation\Interfaces;

use Tustin\PlayStation\Model\Trophy\TrophyTitle;

interface TrophyGroupInterface
{
    public function title(): TrophyTitleInterface;

    public function trophies(): \Iterator;
}
