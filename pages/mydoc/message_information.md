---
title: Message Information
sidebar: mydoc_sidebar
permalink: message_information.html
folder: mydoc
---

The `Playstation\Api\Message` class only has methods that return various properties of a Message.

### Sender

Gets the sender of the message.

```php
$user = $message->sender();
```

Returns an instance of `Playstation\Api\User`.

### Thread

Gets the thread the message is in.

```php
$user = $message->sender();
```

Returns an instance of `Playstation\Api\MessageThread`.

### Body

Gets the message body.

```php
$body = $message->body();
```

Returns a `string`.

### Send Date

Gets the `DateTime` the message was sent.

```php
$dateTime = $message->sendDate();
```

Returns an instance of `\DateTime`.