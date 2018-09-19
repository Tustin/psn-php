---
title: Game Trophies
sidebar: mydoc_sidebar
permalink: game_trophies.html
folder: mydoc
---

## Game Trophies

### Getting a Game's Trophies

Trophy information for a game can be accessed by calling the `trophies` method on any `Playstation\Api\Game` object, assuming the game has trophies available.

```php
// Create the Playstation\Api\Game object.
$game = $client->game('CUSA02290_00');

if ($game->hasTrophies()) {
    $trophyGroups = $game->trophyGroups();

    foreach ($trophyGroups as $trophyGroup) {
        echo sprintf("\t%s has %d trophies\n", $trophyGroup->name(), $trophyGroup->trophyCount());

        foreach ($trophyGroup->trophies() as $trophy) {
            echo sprintf("\t\t[%s] %s - %s (earn rate - %.2f%%)\n", $trophy->type(), $trophy->name(), $trophy->detail(), $trophy->earnedRate());
        }
    }
}
```