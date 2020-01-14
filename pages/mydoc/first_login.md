---
title: First Login
sidebar: mydoc_sidebar
permalink: first_login.html
folder: mydoc
---

Before you begin, copy this JavaScript code:

```js
(function(open) {
    XMLHttpRequest.prototype.open = function(method, url, async, user, pass) {

        this.addEventListener("readystatechange", function() {
            if (this.readyState == XMLHttpRequest.DONE) {
                let response = JSON.parse(this.responseText);

                if (response && "npsso" in response) {
                    console.log('found npsso', response.npsso);
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

1. Navigate to <https://account.sonyentertainmentnetwork.com/> in your browser and open your browser's developer console (typically CTRL + Shift + J).
2. Paste the above Javascript into the console and then login.
3. After the login flow is completed, you should see a new log in the developer console that looks like: `found npsso <64 character code>`. Copy that 64 character code.
4. Login using the PHP code below:

```php
require_once 'vendor/autoload.php';

use PlayStation\Client;

$client = new Client();
//                           v code from above
$client->loginWithNpsso('<64 character npsso code>');

$refreshToken = $client->refreshToken(); // Save this code somewhere (database, file, cache) and use this for future logins
```

You can now call `$client->refreshToken();` to get the refresh token for your account and automate all future logins by following the next authentication method.
