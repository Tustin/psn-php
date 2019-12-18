---
title: First Login
sidebar: mydoc_sidebar
permalink: first_login.html
folder: mydoc
---

1. Enable Two Factor Authentication on your account using the PlayStation website or app.
2. Copy this Javascript code:

```js
(function(open) {
    XMLHttpRequest.prototype.open = function(method, url, async, user, pass) {

        this.addEventListener("readystatechange", function() {
            if (this.readyState == XMLHttpRequest.DONE) {
                let response = JSON.parse(this.responseText);

                if (response && "ticket_uuid" in response) {
                    console.log('found ticket', response.ticket_uuid);
                }
            }
        }, false);

        open.call(this, method, url, async, user, pass);
    };

    window.onbeforeunload = function(){
        return 'Are you sure you want to leave?';
    };

})(XMLHttpRequest.prototype.open);
```

3. Navigate to <https://account.sonyentertainmentnetwork.com/> in your browser and open your browser's developer console (typically CTRL + Shift + J).
4. Paste the above Javascript into the console and then login.
5. When it asks for your 2FA code, **DO NOT** enter it. Instead, look in the browser console window and you should see `found ticket b7aeb485-xxxx-4ec2-zzzz-0f23bcee5bc5`.
6. Copy the above ticket (only the stuff after 'found ticket') and then login to the library like so:

```php
require_once 'vendor/autoload.php';

use PlayStation\Client;

$client = new Client();
//                          v ticket_uuid                 v 2FA code
$client->login('b7aeb485-xxxx-4ec2-zzzz-0f23bcee5bc5', '000000');

$refreshToken = $client->refreshToken();
```

You can now call `$client->refreshToken();` to get the refresh token for your account and automate all future logins by following the next authentication method.