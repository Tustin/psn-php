---
title: Future Logins
sidebar: mydoc_sidebar
permalink: future_logins.html
folder: mydoc
---

{% include note.html content="If you haven't logged in with this library before, you should look at <a href='first_login.html'> First Login</a> first. "%}

Using a refresh token is the easiest and fastest way to login. These tokens can expire but as long as you login frequently and save the new refresh token, you should be fine.

To get a refresh token, you need to have logged into the library once befire using the [First Login](first_login.html) method and save your refresh token.

```php
require_once 'vendor/autoload.php';

use PlayStation\Client;

$client = new Client();
$client->login('b17b5ce5-xxxx-yyyy-zzzz-5996a213b834');

$refreshToken = $client->refreshToken();
```

Again, make sure you save the new refresh token once you login with an existing one. Your old token will still work, but saving the newest tokens is the best way to prevent login issues in the future when the token eventually expires.
