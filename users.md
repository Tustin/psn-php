# Users

## Getting a user

Grabbing users works a bit differently with the new API. As of right now, you need to search for a user if you want to find their account via their online ID. Once you've found their profile, you can cache their account id which makes lookups a lot quicker and easier in the future.

### Searching for a user

```php
$query = $client->users()->search('tustin25');
```

This method should be used when you want to find a user by their online ID, and you don't have their account ID stored already.

The `search` method won't actually return the full list of search results. Instead, this will return an [Iterator](https://www.php.net/manual/en/class.iterator.php) which will only fetch results from the API when the data is needed. This is useful in an instance where you might only want a few of the first search results, but it also allows for you to potentially fetch every user that matches your search while only fetching data when it's needed.

### Finding a user via account ID

```php
$user = $client->users()->find('4421126145254737307');
```

This will return the users' profile with this exact account ID.

### Getting your user profile

```php
$me = $client->users()->me();
```

This will return the authenticated user's profile.

## Available methods

Once you've obtained a user, you can call many methods on the object for information. psn-php will cache any existing API data and will try to fetch the data from cache first so you don't have to send unnecessary requests to the API.

<style>
    #collection-method-list > p {
        column-count: 3; -moz-column-count: 3; -webkit-column-count: 3;
        column-gap: 2em; -moz-column-gap: 2em; -webkit-column-gap: 2em;
    }

    #collection-method-list a {
        display: block;
    }
</style>

<div id="collection-method-list">

### All methods
[aboutMe](#aboutme)
[accountId](#accountid)
[avatarUrl](#avatarUrl)
[avatarUrls](#avatarurls)
[followerCount](#followercount)
[friends](#friends)
[gameList](#gameList)
[hasFriendRequested](#hasfriendrequested)
[hasMutualFriends](#hasmutualfriends)
[hasPlus](#hasplus)
[isBlocking](#isblocking)
[isCloseFriend](#isclosefriend)
[isFollowing](#isfollowing)
[isOnline](#isonline)
[isVerified](#isverified)
[languages](#languages)
[mutualFriendCount](#mutualfriendcount)
[onlineId](#onlineid)
[trophySummary](#trophySummary)
[trophyTitles](#trophyTitles)

</div>

### aboutMe

Gets the user's about me.

```php
$user->aboutMe();

// How're ya now?
```

### accountId

Gets the user's account ID. This is used for finding this user's profile in the future.

```php
$user->accountId();

// 4421126145254737307
```

### avatarUrl

Gets the user's avatar url. It will return the highest quality image possible.

```php
$user->avatarUrl();

// 'https://example.com/xl.png'
```

### avatarUrls

Gets the user's avatar urls. This will return an array of all available image sizes.

```php
$urls = $user->avatarUrls();

// Some of these may be missing, depending on the user's avatar.
// ['xl' => 'https://example.com/xl.png', 'l' => 'https://example.com/l.png', 'm' => 'https://example.com/m.png', 's' => 'https://example.com/s.png']
```

### followerCount

Gets the user's follower count.

```php
$user->followerCount();

// 2
```

### friends

Gets the user's friends list.

This method returns an [Iterator](https://www.php.net/manual/en/class.iterator.php), which can also have filters applied to only return specific users.

```php
$friends = $user->friends(); // Get every friend
$friends = $user->friends()->onlineIdContains('test'); // Get a friend with their online ID containing 'test'.
$friends = $user->friends()->verified(); // Get verified friends.
$friends = $user->friends()->closeFriends(); // Get close friends.
$friends = $user->friends()->closeFriends()->onlineIdContains('test'); // You can chain filters too!

foreach ($friends as $friend) {
    $friend->onlineId();
    // my_good_friend1
}
```

### gameList

Gets the user's played games. These will only return PS4 and PS5 titles, and these **DO NOT** contain the trophies (use [trophies](#trophies) for that).

This method returns an [Iterator](https://www.php.net/manual/en/class.iterator.php) of [GameTitle](games.md).

```php
foreach ($user->gameList() as $game) {
    $game->name();
    // God of War
}
```

### hasFriendRequested

Checks if the authenticated user has friend requested this user.

```php
$user->hasFriendRequested();

// false
```

### hasMutualFriends

Checks if the authenticated user has any mutual friends with this user.

```php
$user->hasMutualFriends();

// false
```

### hasPlus

Checks if the user has PlayStation Plus.

```php
$user->hasPlus();

// true
```

### isBlocking

Checks if the authenticated user is blocking this user.

```php
$user->isBlocking();

// false
```

### isCloseFriend

Checks if the authenciated user is a close friend of this user (sharing real name).

```php
$user->isCloseFriend();

// false
```

### isFollowing

Checks if the authenticated user is following this user.

```php
$user->isFollowing();

// true
```

### isOnline

Checks if the user is currently online.

```php
$user->isOnline();

// true
```

### isVerified

Checks if the user is verified.

```php
$user->isVerified();

// false
```

### languages

Gets the languages for the user.

```php
$user->languages();

// ['en', 'jp']
```


### mutualFriendCount

Gets the amount of mutual friends the authenticated user and this user share.

```php
$user->mutualFriendCount();

// 2
```

### onlineId

Gets the user's online ID.
```php
$user->onlineId();

// tustin25
```

### trophySummary

Gets the user's trophy summary. This includes data like trophy level, trophy rarity counts, etc.

Returns an instance of [TrophySummary](trophy.md?#summary)

```php
$summary = $user->trophySummary();

$summary->level();

// 500
```

### trophyTitles

Gets the user's trophy list. This returns every game that has trophy data on this user's profile.

This method returns an [Iterator](https://www.php.net/manual/en/class.iterator.php), which can also have filters applied to only return specific trophy titles.

```php
$titles = $user->trophyTitles(); // Get every trophy title
$titles = $user->trophyTitles()->hasTrophyGroups(); // Get trophy titles that only have trophy groups (ex: DLC trophies)
$titles = $user->trophyTitles()->withName('call of duty'); // Get every trophy title containing this name
$titles = $user->trophyTitles()->withName('call of duty')->hasTrophyGroups(); // You can chain filters too!

foreach ($titles as $title) {
    $title->name();
    // Call of Duty: Vanguard
}
```