---
title: Message Thread Management
sidebar: mydoc_sidebar
permalink: message_thread_management.html
folder: mydoc
---

To obtain an instance of `Playstation\Api\MessageThread`, please read the [User documentation for Message Threads](user_messaging.html#all-message-threads).

## Users

### Leave

Leave the message thread.

```php
$thread->leave();
```

## Settings

### Favorite

Favorite the message thread.

```php
$thread->favorite();
```

### Unfavorite

Unfavorite the message thread.

```php
$thread->unfavorite();
```

## Thumbnail

### Set Thumbnail Image

Sets an image as the thread thumbnail.

`setThumbnail` takes one parameter of type `string`, which is the raw byte data of the image.

```php
$thread->setThumbnail(file_get_contents('https://i.imgur.com/FLVEUp0.png'));
```

### Remove Thumbnail Image

Removes the current thread's thumbnail.

```php
$thread->removeThumbnail();
```

## Name

### Set Name

Sets the name of the thread.

`setName` takes one parameter of type `string`, which is the name for the thread.

```php
$thread->setName('psn-php!');
```