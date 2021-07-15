---
title: Getting a User
sidebar: mydoc_sidebar
permalink: getting_user.html
folder: mydoc
---

Grabbing users works a bit differently with the new API. As of right now, you need to search for a user if you want to find their account via their online ID. Once you've found their profile, you can cache their account id which makes lookups a lot quicker and easier in the future.

These methods can all be access by calling the `users` method on the `Tustin\PlayStation\Client` class. They all return instances of `Tustin\PlayStation\Model\User`.

### Searching for a user

```php
$query = $client->users()->search('tustin25');
```

The `search` method will return an instance of a `UserSearchIterator` (TODO: document Iterator methods...)


### Finding a user via account ID

```php
// Your information
$user = $client->users()->find('4421126145254737307');
```

### Getting your user profile

```php
$me = $client->users()->me();
```