---
title: User Management
sidebar: mydoc_sidebar
permalink: user_management.html
folder: mydoc
---

With a `Playstation\Api\User` object, you can perform many actions on the user.

To see how to get a `Playstation\Api\User` object, please read [Getting A User](getting_user.html).

## Friend Management

### Add

Adds the user to friends list.

`add` takes an optional parameter of `string`, which is the message to send with the request.

```php
$user->add('Add me!');
```

### Remove

Removes the user from friends list.

```php
$user->remove();
```

### Block

Blocks the user.

```php
$user->block();
```

### Unblock

Unblocks the user.

```php
$user->unblock();
```
