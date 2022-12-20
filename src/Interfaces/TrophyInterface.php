<?php

namespace Tustin\PlayStation\Interfaces;

interface TrophyInterface
{
    public function title(): TrophyTitleInterface;

    public function group(): TrophyGroupInterface;

}
