# PSN-PHP Wrapper

A PHP wrapper for the PlayStation API.


[![GitHub stars](https://img.shields.io/github/stars/Tustin/psn-php.svg)](https://github.com/Tustin/psn-php/stargazers)
[![GitHub license](https://img.shields.io/github/license/Tustin/psn-php.svg)](https://github.com/Tustin/psn-php/blob/master/LICENSE)

## Getting Started

Pull in the project with composer:
`composer require tustin/psn-php`

### Authenticating

You cannot login using the traditional method with this library due to Sony incorperating ReCaptcha2 on all PlayStation login forms. As a result, you can only login using two methods.

#### With Two Factor Authentication

1. Enable Two Factor Authentication on your account using the PlayStation website or app.
2. Navigate to https://www.bungie.net/en/User/SignIn/Psnid?code=000000 in your browser and login using the PlayStation website.
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

5. You can now call `$client->refreshToken();` to get the refresh token for your account and automate all future logins by following the next authentication method.

#### With A Refresh Token

1. Using a refresh token is the easiest and fastest way to login. These tokens can expire but as long as you login frequently and save the new refresh token, you should be fine.

```php
require_once 'vendor/autoload.php';

use PlayStation\Client;

$client = new Client();
$client->login('b17b5ce5-xxxx-yyyy-zzzz-5996a213b834');

$refreshToken = $client->refreshToken();
```

2. Again, make sure you save the new refresh token once you login with an existing one. Your old token will still work, but saving the newest tokens is the best way to prevent login issues in the future when the token eventually expires.

## Disclaimer

This project was not intended to be used for spam, abuse, or anything of the sort. Any use of this project for those purposes is not endorsed. Please keep this in mind when creating applications using this API wrapper.
