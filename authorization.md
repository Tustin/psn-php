# Authorization

Unfortunately, Sony has made it a bit cumbersome to authenticate with PlayStation Network. As a result, you will need to use some more involved methods to authenticate a user.

## First Login

The easiest way to login to PlayStation for the first time using this library is via your NPSSO token. Once you login with this token, you can retrieve a refresh token to use
for future logins, OR you can keep using this NPSSO token. I've found that the NPSSO token tends to last longer before expiring.

### Easy Method

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

### Verbose Method

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


## Future Logins

For future logins, you _can_ continue to login with the NPSSO, but for security reasons, using the refresh token is the preferred method. These tokens can expire but as long as you login frequently and save the new refresh token, you should be fine.

```php
require_once 'vendor/autoload.php';

use Tustin\PlayStation\Client;

$client = new Client();
$client->login('b17b5ce5-xxxx-yyyy-zzzz-5996a213b834');

$refreshToken = $client->getRefreshToken()->getToken();
```

Again, make sure you save the new refresh token once you login with an existing one. Your old token will still work, but saving the newest tokens is the best way to prevent login issues in the future when the token eventually expires.