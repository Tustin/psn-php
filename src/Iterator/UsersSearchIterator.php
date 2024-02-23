<?php

namespace Tustin\PlayStation\Iterator;

use Iterator;
use Tustin\PlayStation\User;
use Tustin\PlayStation\Client;
use Tustin\PlayStation\ApiRequest;
use Tustin\PlayStation\OAuthToken;
use Tustin\PlayStation\Search\UserSearchRequest;

class UsersSearchIterator extends AbstractApiIterator
{
    public function __construct(private UserSearchRequest $request)
    {
        $this->usesCustomCursor();

        $this->access($this->request->offset);
    }

    /**
     * Accesses a new page of results.
     */
    public function access(mixed $cursor): void
    {
        $request = new ApiRequest(
            accessToken: OAuthToken::accessToken(),
            apiBaseUrl: Client::$apiBaseUrl,
        );

        $this->request->setOffset($cursor);

        $response = $request->post(
            $this->request::getSearchUri(),
            $this->request->toArray()
        );

        $domainResponses = $response->json('domainResponses');

        // Always get the first domain response
        $domainResponse = $domainResponses[0];

        $this->update($domainResponse['totalResultCount'], $domainResponse['results'], empty($domainResponse['next']) ? null : $domainResponse['next']);
    }

    /**
     * Gets the current user in the iterator.
     */
    public function current(): User
    {
        $socialMetadata = $this->getFromOffset($this->currentOffset);

        if (!array_key_exists('socialMetadata', $socialMetadata)) {
            throw new \RuntimeException('Social metadata not found in search results');
        }

        return User::constructFrom(
            $socialMetadata['socialMetadata']
        );
    }
}
