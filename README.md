# PHP Interface for PSN API
These tools allow you to interface with the official (private) PSN API created by Sony.

## Usage
### Setting up
Download the project files, and place them on your server or localhost. Create an index.php file.

Require the autoload.php file like so:
```php
require_once("autoload.php");
```
You're now ready to begin.
### Authenticating
It's smooth like butter (well, it should be anyways!). Firstly, create a new instance of the Auth class, like so:
```php
$account = new \PSN\Auth\Auth("example@email.com", "password");
```
Where **example@email.com** is your email, and **password** is, well, your password. Assuming everything is setup properly and your PSN credentials are correct, you shouldn't see any text on the screen. Good stuff!

Now let's try grabbing our info. Try something like so:
```php
echo "<h1>Welcome, " . $account->GetInfo()->profile->onlineId . "!</h1>";
```
This should display your PSN name in big text on the screen.

#### More functionality will be added soon, such as interacting with friends, viewing trophy data, messaging and more. Stay tuned.