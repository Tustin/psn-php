---
title: Getting a User
sidebar: mydoc_sidebar
permalink: getting_user.html
folder: mydoc
---

You can grab a user by calling the `user` method on the `PlayStation\Client` object you created to login with. This method works both ways, allowing you to grab your own information (the logged in user) or by grabbing someone else's information by supplying their `onlineId` to the `user` method:

```php
// Your information
$me = $client->user();

// Someone else
$user = $client->user('Hakoom');
```

`user` will return an instance of the `Playstation\Api\User` class.