---
title: Sending Messages
sidebar: mydoc_sidebar
permalink: message_thread_messages.html
folder: mydoc
---

To obtain an instance of `Playstation\Api\MessageThread`, please read the [User documentation for Message Threads](user_messaging.html#all-message-threads).

## Messaging

### Text Message

Sends a message containing only text.

`sendMessage` takes one parameter of `string`, which is the message to send to the thread.

```php
$message = $thread->sendMessage('Hello!');
```

Returns a new instance of `Playstation\Api\Message` or null if the message failed to send.

### Image Message

Sends a message containing an image.

`sendImage` takes one parameter of `string`, which is the raw byte data of the image.

```php
$message = $thread->sendImage(file_get_contents('https://i.imgur.com/FLVEUp0.png'));
```

Returns a new instance of `Playstation\Api\Message` or null if the message failed to send.

### Audio Message

Sends a message containing audio.

`sendAudio` takes two parameters. One of type `string`, which is the raw byte data of the audio. Second is of type `int`, which is the length in seconds of the audio.

```php
$message = $thread->sendAudio(file_get_contents('some_audio.mp3'), 15);
```

Returns a new instance of `Playstation\Api\Message` or null if the message failed to send.