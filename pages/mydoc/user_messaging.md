---
title: Messaging a User
sidebar: mydoc_sidebar
permalink: user_messaging.html
folder: mydoc
---

With a `Playstation\Api\User` object, you can perform many actions on the user.

To see how to get a `Playstation\Api\User` object, please read [Getting A User](getting_user.html).

## Messaging

### Text Message

Sends a message containing only text.

`sendMessage` takes one parameter of `string`, which is the message to send to the user.

```php
$message = $user->sendMessage('Hello!');
```

Returns a new instance of `Playstation\Api\Message` or null if the message failed to send.

### Image Message

Sends a message containing an image.

`sendImage` takes one parameter of `string`, which is the raw byte data of the image.

```php
$message = $user->sendImage(file_get_contents('https://i.imgur.com/FLVEUp0.png'));
```

Returns a new instance of `Playstation\Api\Message` or null if the message failed to send.

### Audio Message

Sends a message containing audio.

`sendAudio` takes two parameters. One of type `string`, which is the raw byte data of the audio. Second is of type `int`, which is the length in seconds of the audio.

```php
$message = $user->sendAudio(file_get_contents('some_audio.mp3'), 15);
```

Returns a new instance of `Playstation\Api\Message` or null if the message failed to send.

## Message Threads

### All Message Threads

Returns all message threads containing both the current user and the logged in user.

```php
$threads = $user->messageThreads();
```

Returns an `array` of `Playstation\Api\MessageThread`.

### Private Message Thread

Returns the message thread containing only the current user and the user you are logged in with.

```php
$thread = $user->privateMessageThread();
```

Returns an instance of `Playstation\Api\MessageThread` or null if there is no private message thread.
