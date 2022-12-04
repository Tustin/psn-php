<?php

namespace Tustin\PlayStation\Iterator;

use Tustin\PlayStation\Factory\FriendsListFactory;
use Tustin\PlayStation\Model\User;

class FriendsListIterator extends AbstractApiIterator
{
    private string $userAccountId;
    private array $cachedAccounts = [];

    public function __construct(FriendsListFactory $friendsListFactory, string $userAccountId)
    {
        parent::__construct($friendsListFactory->getHttpClient());
        $this->userAccountId = $userAccountId;
        $this->limit = 100;
        $this->access(0);
    }

    public function access(mixed $cursor): void
    {
        $results = $this->get('userProfile/v1/internal/users/' . $this->userAccountId . '/friends', [
            'limit' => $this->limit,
            'offset' => $cursor,
            'order' => 'availability+realName+onlineId'
        ]);

        // Batch-fetch these friends so that we can run filters over the properties without needing to fetch each individual profile.
        $friendDetails = $this->get('userProfile/v1/internal/users/profiles', [
            'accountIds' => implode(',', $results->friends)
        ]);

        $this->cachedAccounts = array_merge($this->cachedAccounts, $friendDetails->profiles);

        $this->update($results->totalItemCount, $results->friends);
    }

    public function current(): User
    {
        // Because there's no accountId prop in the batch profile responses, we need to manually add it.
        // Cmon Sony...
        $this->cachedAccounts[$this->currentOffset]->accountId = $this->cache[$this->currentOffset];

        return User::fromObject(
            $this->getHttpClient(),
            $this->cachedAccounts[$this->currentOffset]
        );
    }
}
