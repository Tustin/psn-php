# Store

You can use this library to query the PlayStation Store for title information, represented as _concepts_. Each PlayStation Store item is internally referred as a concept by Sony.

## Performing a Store search

```php
use Tustin\PlayStation\Factories\Store;

$results = Store::search('call of duty', limit: 50);

foreach ($results as $concept) {
    echo $concept->name() . "\n";
}
```

Searching the store will return an `StoreSearchIterator` which will automatically query the PlayStation Store API to retrieve results using the `limit` provided (this defaults to 20). It will only query for more information when needed to save on resources.

The `StoreSearchIterator` will return instances of `Tustin\PlayStation\Models\Store\Concept` which represent the Store item.
