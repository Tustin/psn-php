# Trophies

Trophies can be retrieved in two ways:

1. Using the NP Communication ID for the trophy title. This will be in a format like `NPWR08899_00` and is unique to every trophy title.
2. From a `Tustin\PlayStation\Models\User` object to retrieve a user's trophies.

## Using the NP Communication ID

```php
use Tustin\PlayStation\Models\Trophies\TrophyTitle;
use Tustin\PlayStation\Enums\TrophyServiceName;

$trophyTitle = new TrophyTitle("NPWR08899_00", serviceName: TrophyServiceName::Trophy)
```

The `serviceName` parameter is determined based on the trophy title platform. For PS3, PS4, and PS Vita titles, use `TrophyServiceName::Trophy`. For PS5 and PC titles, use `TrophyServiceName::Trophy2`. Failure to use the right service name will cause a bad response from the API.

## Finding a user's trophy titles

```php
use Tustin\PlayStation\Factories\Users;

// Search for the user first.
$user = Users::search('tustin25')->first();
$trophyTitles = $user->trophyTitles();

// Or if you already have their account id...
$trophyTitles = new UserTrophyTitles('4421126145254737307');

foreach ($trophyTitles as $trophyTitle) {
    echo $trophyTitle->name() . ' - ' . $trophyTitle->earnedTrophiesCount() . '/' . $trophyTitle->trophyCount() . "\n";
}
```

The `trophyTitles` method on the `User` object will return `Tustin\PlayStation\Factories\UserTrophyTitles`. Internally, this is just normal Iterator, but it allows you to add custom filters to the results. An example:

```php
// Give me trophy titles for cod only.
$trophyTitles = $user->trophyTitles()->withName('call of duty');

// Actually I just want any trophy titles that have trophy groups
$trophyTitles = $user->trophyTitles()->hasTrophyGroups();
```

These filters allow you to filter trophy titles that contain the words `call of duty` in it's name, or filter trophy titles that trophy groups. You can either use this or not, and you can also chain them together if you'd like.

When retrieving a user's trophy titles, you will also have access to more information like the user's earned trophy count. So sometimes it can
