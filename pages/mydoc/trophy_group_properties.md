---
title: Trophy Group Properties
sidebar: mydoc_sidebar
permalink: trophy_group_properties.html
folder: mydoc
---

## Trophy Group Properties

### Id

Get the trophy group ID.

```php
$id = $trophyGroup->id();
```

Returns an `int`.

### Name

Returns the name of the trophy group.

```php
$name = $trophyGroup->name();
```

Returns a `string`.

### Detail

Returns the trophy group details.

```php
$detail = $trophyGroup->detail();
```

Returns a `string`.

### Icon URL

Returns the trophy group icon URL.

```php
$iconUrl = $trophyGroup->iconUrl();
```

Returns a `string`.

### Trophy Count

Returns the total amount of trophies in the trophy group.

```php
$trophyCount = $trophyGroup->trophyCount();
```

Returns an `int`.

### Progress

Returns the completion progress of the trophy group.

```php
$progress = $trophyGroup->progress();
```

Returns an `int`.

### Last Earned Date

Returns the DateTime the last trophy in the group was earned.

```php
$lastEarnedDate = $trophyGroup->lastEarnedDate();
```

Returns a new instance of `\DateTime`.

### Trophies

Returns all the trophies in the trophy group.

```php
$trophies = $trophyGroup->trophies();
```

Returns an `array` of `Playstation\Api\Trophy`.

### Game

Returns the game the trophy group belongs to.

```php
$game = $trophyGroup->game();
```

Returns an instance of `Playstation\Api\Game`.
