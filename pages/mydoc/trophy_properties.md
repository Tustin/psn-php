---
title: Trophy Properties
sidebar: mydoc_sidebar
permalink: trophy_properties.html
folder: mydoc
---

To see how to get a `Playstation\Api\Trophy` object, please read either:

[Getting a User's Trophies](user_trophies.html)

[Getting a Game's Trophies](game_trophies.html)

## Trophy Properties

### Id

Get the trophy ID.

```php
$id = $trophy->id();
```

Returns an `int`.

### Hidden

Returns if the trophy is hidden or not.

```php
$hidden = $trophy->hidden();
```

Returns a `bool`.

### Trophy Type

Gets the type of Trophy (bronze, silver, gold, platinum).

```php
$type = $trophy->type();
```

Returns a `string`.

### Name

Returns the name of the trophy.

```php
$name = $trophy->name();
```

Returns a `string`.

### Detail

Returns the trophy details.

```php
$detail = $trophy->detail();
```

Returns a `string`.

### Icon URL

Returns the trophy icon URL.

```php
$iconUrl = $trophy->iconUrl();
```

Returns a `string`.

### Earned Rate

Returns the earned rate percentage of the trophy.

```php
$earnedRate = $trophy->earnedRate();
```

Returns a `float`.

### Earned

Returns whether or not the user has earned the trophy.

```php
$hasEarned = $trophy->earned();
```

Returns a `bool`.

### Earned Date

Returns the DateTime the trophy was earned, if it has been earned.

```php
$earnedDate = $trophy->earnedDate();
```

Returns a new instance of `\DateTime`, or null if the trophy hasn't been earned.

### Trophy Group

Returns the trophy group the trophy is in.

```php
$trophyGroup = $trophy->trophyGroup();
```

Returns an instance of `Playstation\Api\TrophyGroup`.

### Game

Returns the game the trophy belongs to.

```php
$game = $trophy->game();
```

Returns an instance of `Playstation\Api\Game`.
