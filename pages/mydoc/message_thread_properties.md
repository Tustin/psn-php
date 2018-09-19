---
title: Message Thread Properties
sidebar: mydoc_sidebar
permalink: message_thread_properties.html
folder: mydoc
---

To obtain an instance of `Playstation\Api\MessageThread`, please read the [User documentation for Message Threads](user_messaging.html#all-message-threads).

## Message Thread Properties

### Members

Gets all the members in the message thread.

```php
$members = $thread->members();
```

Returns an `array` of `Playstation\Api\User`.

### Message Thread ID

Returns the message thread ID.

```php
$threadId = $thread->messageThreadId();
```

Returns a `string`.

### Member Count

Gets the member count.

```php
$memberCount = $thread->memberCount();
```

Returns an `int`.

### Name

Gets the message thread name.

```php
$name = $thread->name();
```

Returns a `string`.

### Thumbnail URL

Gets the thumbnail image URL of the message thread.

```php
$thumbnail = $thread->thumbnailUrl();
```

Returns a `string`.

### Modified Date

Gets the last time the message thread was modified.

```php
$date = $thread->modifiedDate();
```

Returns a new instance of `\DateTime`.
