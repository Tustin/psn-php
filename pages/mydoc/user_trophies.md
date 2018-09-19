---
title: User Trophies
sidebar: mydoc_sidebar
permalink: user_trophies.html
folder: mydoc
---

## User Trophies

### Getting a User's Trophies

User trophies can be accessed by calling the `games` method inside any `Playstation\Api\User` object. This will return all the games the user has played, which will contain all the game trophies and which ones they've earned.

```php
// Your games
$games = $client->user()->games();

// Someone else's games
$games = $client->user('tustin25')->games();

foreach ($games as $game) {
    if ($game->hasTrophies()) {
        $trophyGroups = $game->trophyGroups();

        foreach ($trophyGroups as $trophyGroup) {
            echo sprintf("\t%s has %d trophies\n", $trophyGroup->name(), $trophyGroup->trophyCount());

            foreach ($trophyGroup->trophies() as $trophy) {
                if (!$trophy->earned()) continue; // Skip unearned trophies.

                echo sprintf("\t\t[%s] %s - %s (earn rate - %.2f%%)\n", $trophy->type(), $trophy->name(), $trophy->detail(), $trophy->earnedRate());
            }
        }
    }
}
```