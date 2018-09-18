---
title: First Login
sidebar: mydoc_sidebar
permalink: first_login.html
folder: mydoc
---

1. Enable Two Factor Authentication on your account using the PlayStation website or app.
2. Navigate to <https://www.bungie.net/en/User/SignIn/Psnid?code=000000> in your browser and login using the PlayStation website.
3. When it asks for your 2FA code, **DO NOT** enter it. Instead, look in the URL. You should see a parameter called `ticket_uuid`.
4. Get the value of `ticket_uuid` from the URL (it will look similar to this: `b7aeb485-xxxx-4ec2-zzzz-0f23bcee5bc5`) and the 2FA code from your device. You can now login using the library like so:

```php
require_once 'vendor/autoload.php';

use PlayStation\Client;

$client = new Client();
//                          v ticket_uuid                 v 2FA code
$client->login('b7aeb485-xxxx-4ec2-zzzz-0f23bcee5bc5', '000000');

$refreshToken = $client->refreshToken();
```

You can now call `$client->refreshToken();` to get the refresh token for your account and automate all future logins by following the next authentication method.