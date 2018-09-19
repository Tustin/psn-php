---
title: User Properties
sidebar: mydoc_sidebar
permalink: user_properties.html
folder: mydoc
---

To see how to get a `PlayStation\Api\User` object, please read [Getting A User](getting_user.html).

## User Properties

### Online ID

Returns the user's online ID.

```php
$onlineId = $user->onlineId();
```

Returns a `string`.

### About Me

Returns the user's about me.

```php
$about = $user->aboutMe();
```

Returns a `string`.

### Following

Returns whether or not the logged in user is following the current user.

```php
$following = $user->following();
```

Returns a `bool`.

### Follower Count

Returns the user's follower count.

```php
$followers = $user->followerCount();
```

Returns an `int`.

### Verified

Returns whether or not the user is verified.

```php
$verified = $user->verified();
```

Returns a `bool`.

### Avatar URL

Returns the user's avatar URL.

```php
$avatar = $user->avatarUrl();
```

Returns a `string`.

### Are Friends 

Returns whether or not the logged in user is friends with the current user.

```php
$areFriends = $user->friend();
```

Returns a `bool`.

### Are Close Friends

Returns whether or not the logged in user is close friends with the current user.

```php
$areCloseFriend = $user->closeFriend();
```

Returns a `bool`.

### Friends

Returns a list of all the user's friends.

`friends` takes two optional parameters:

`filter`: Deprecated, but defaulted to `online`.

`limit`: How many friends to return.

```php
$friends = $user->friends();
```

Returns an `array` of `PlayStation\Api\User`.

### Games

Returns a list of the user's games played.

`games` takes one optional parameter:

`limit`: How many games to return.

```php
$games = $user->games();
```

Returns an `array` of `PlayStation\Api\Game`.