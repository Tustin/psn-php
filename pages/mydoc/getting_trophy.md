---
title: Getting Trophies
sidebar: mydoc_sidebar
permalink: getting_trophy.html
folder: mydoc
---

## Trophies

### Game Trophies

Trophy information for a game can be accessed by calling the `trophies` method on any `Playstation\Api\Game` object, assuming the game has trophies available.

```php
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


### User Trophies

You can grab trophy information by calling the `trophy` method on the `PlayStation\Client` object you created to login with. This method works both ways, allowing you to grab your own information (the logged in user) or by grabbing someone else's information by supplying their `onlineId` to the `user` method:

```php
// Your information
$me = $client->user();

// Someone else
$user = $client->user('Hakoom');
```

`user` will return an instance of the `Playstation\Api\User` class.