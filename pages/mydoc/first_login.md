---
title: First Login
sidebar: mydoc_sidebar
permalink: first_login.html
folder: mydoc
---


The easiest way to login to PlayStation for the first time using this library is via your NPSSO token. Once you login with this token, you can retrieve a refresh token to use
for future logins, OR you can keep using this NPSSO token. I've found that the NPSSO token tends to last longer before expiring.

## Easy Method

1. Login [to the official PlayStation website](https://www.playstation.com/)
2. Visit [this page](https://ca.account.sony.com/api/v1/ssocookie) and copy your NPSSO token.
3. Login using the PHP code below:

```php
require_once 'vendor/autoload.php';

use Tustin\PlayStation\Client;

$client = new Client();
//                           v code from above
$client->loginWithNpsso('<64 character npsso code>');

$refreshToken = $client->getRefreshToken()->getToken(); // Save this code somewhere (database, file, cache) and use this for future logins
```

## Verbose Method

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

use Tustin\PlayStation\Client;

$client = new Client();
//                           v code from above
$client->loginWithNpsso('<64 character npsso code>');

$refreshToken = $client->getRefreshToken()->getToken(); // Save this code somewhere (database, file, cache) and use this for future logins
```