<?php

namespace Tustin\PlayStation;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Tustin\PlayStation\Exceptions\UnmappedGraphQLOperationException;

class Api
{
    protected ?Client $httpClient = null;

    private ?Response $lastResponse = null;

    public function __construct(Client $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Gets the Guzzle HTTP client.
     */
    public function getHttpClient(): ?Client
    {
        return $this->httpClient;
    }

    /**
     * Sends a GET request.
     */
    public function get(string $path = '', array $body = [], array $headers = []): mixed
    {
        return ($this->lastResponse = $this->getHttpClient()->get($path, [
            'query' => $body,
            'headers' => $headers
        ]))->getBody()->jsonSerialize();
    }

    /**
     * Sends a GET request and reads the raw response.
     */
    public function getRaw(string $path = '', array $body = [], array $headers = []): mixed
    {
        return $this->getHttpClient()->get($path, [
            'query' => $body,
            'headers' => $headers
        ])->getBody()->getContents();
    }

    /**
     * Sends a POST request as form data.
     */
    public function post(string $path, array $body, array $headers = []): mixed
    {
        return ($this->lastResponse = $this->getHttpClient()->post($path, [
            'form_params' => $body,
            'headers' => $headers
        ]))->getBody()->jsonSerialize();
    }

    /**
     * Sends a POST request as JSON data.
     */
    public function postJson(string $path, array $body, array $headers = []): mixed
    {
        return ($this->lastResponse = $this->getHttpClient()->post($path, [
            'json' => $body,
            'headers' => $headers
        ]))->getBody()->jsonSerialize();
    }

    /**
     * Sends a multi-part POST request.
     */
    public function postMultiPart(string $path, array $body, array $headers = []): mixed
    {
        return ($this->lastResponse = $this->getHttpClient()->post($path, [
            'multipart' => $body,
            'headers' => $headers
        ]))->getBody()->jsonSerialize();
    }

    /**
     * Sends a DELETE request.
     */
    public function delete(string $path, array $headers = []): mixed
    {
        return ($this->lastResponse = $this->getHttpClient()->delete($path, [
            'headers' => $headers
        ]))->getBody()->jsonSerialize();
    }

    /**
     * Sends a DELETE request with JSON data.
     */
    public function deleteJson(string $path, array $body, array $headers = []): mixed
    {
        return ($this->lastResponse = $this->getHttpClient()->delete($path, [
            'json' => $body,
            'headers' => $headers
        ]))->getBody()->jsonSerialize();
    }

    /**
     * Sends a PATCH request.
     */
    public function patch(string $path, array $headers = []): mixed
    {
        return ($this->lastResponse = $this->getHttpClient()->patch($path, [
            'headers' => $headers
        ]))->getBody()->jsonSerialize();
    }

    /**
     * Sends a PUT request with form data.
     */
    public function put(string $path, array $body = [], array $headers = []): mixed
    {
        return ($this->lastResponse = $this->getHttpClient()->put($path, [
            'form_params' => $body,
            'headers' => $headers
        ]))->getBody()->jsonSerialize();
    }

    /**
     * Sends a PUT request with JSON data.
     */
    public function putJson(string $path, array $body = [], array $headers = []): mixed
    {
        return ($this->lastResponse = $this->getHttpClient()->put($path, [
            'json' => $body,
            'headers' => $headers
        ]))->getBody()->jsonSerialize();
    }

    /**
     * Sends a PUT request that allows a string body, typically for raw file bytes.
     */
    public function putFile(string $path, array $body = [], array $headers = []): mixed
    {
        return ($this->lastResponse = $this->getHttpClient()->put($path, [
            'body' => $body,
            'headers' => $headers
        ]))->getBody()->jsonSerialize();
    }

    /**
     * Sends a multi-part PUT request.
     */
    public function putMultiPart(string $path, array $body = [], array $headers = []): mixed
    {
        return ($this->lastResponse = $this->getHttpClient()->put($path, [
            'multipart' => $body,
            'headers' => $headers
        ]))->getBody()->jsonSerialize();
    }

    /**
     * Streams a file.
     */
    public function stream(string $path, array $headers = []): string
    {
        return ($this->lastResponse = $this->getHttpClient()->get($path, [
            'headers' => $headers,
            'stream' => true
        ]))->getBody();
    }

    /**
     * Returns the last response that was received (if any).
     */
    public function getLastResponse(): ?Response
    {
        return $this->lastResponse;
    }

    public function graphql(string $op, array $variables): object
    {
        $hashMap = [
            'metGetConceptByProductIdQuery' => '0a4c9f3693b3604df1c8341fdc3e481f42eeecf961a996baaa65e65a657a6433',
            'metGetConceptById' => 'cc90404ac049d935afbd9968aef523da2b6723abfb9d586e5f77ebf7c5289006',
            'metGetProductById' => 'a128042177bd93dd831164103d53b73ef790d56f51dae647064cb8f9d9fc9d1a',
            'metGetAddOnsByTitleId' => 'e98d01ff5c1854409a405a5f79b5a9bcd36a5c0679fb33f4e18113c157d4d916',
            'metGetCategoryGrid' => 'b67a9e4414b80d8d762bf12a588c6125467ae0bb3bbe3cee3f7696c6984f8ef6',
            'metGetCategoryGrids' => 'cc0b6513521c59a321bf62334fa23a92f22cd2ce1abe9f014fadac6379e414a8',
            // 'metGetCategoryStrand'=> '',
            'metGetCategoryStrands' => '55ab5f168bec56f8362b5519f59faaf786d4e1cfeabb8bc969d6a65545e14f4d',
            'metGetDefaultView' => 'bec1b8a3b0bae8c08e3ce2c7fe2f38a69343434ccfbcdd82cc1f2e44f86b7c40',
            // 'metGetFilterAndSortItemsCount'=> '',
            'metGetPricingDataByConceptId' => 'abcb311ea830e679fe2b697a27f755764535d825b24510ab1239a4ca3092bd09',
            'metGetStoreWishlist' => '571149e8aa4d76af7dd33b92e1d6f8f828ebc5fa8f0f6bf51a8324a0e6d71324',
            'metGetViews' => '6fd98ff7fecb603006fb5d92db176d5028435be163c8d1ee9f7c598ab4677dd1',
            'metGetWebCheckoutCart' => '2d4165c4de76877a32f3d08c91ce2af0e01d69300131fed0a8022868235e85b1',
            // 'metGetWishlistedItemIds'=> '',
            'metGetExperience' => '054e61ee68bbeadc21435caebcc4f2bba0919a99b06629d141b0b82dc55f10c4',
            'metGetContextSearchResults' => 'ac5fb2b82c4d086ca0d272fba34418ab327a7762dd2cd620e63f175bbc5aff10',
            'metGetDomainSearchResults' => '23ece284bf8bdc50bfa30a4d97fd4d733e723beb7a42dff8c1ee883f8461a2e1',
        ];

        if (!array_key_exists($op, $hashMap)) {
            throw new UnmappedGraphQLOperationException('The GraphQL operation ' . $op . ' is not mapped.');
        }

        return $this->get('graphql/v1/op', [
            'operationName' => $op,
            'variables' => json_encode($variables),
            'extensions' => json_encode([
                'persistedQuery' => [
                    'version' => 1,
                    'sha256Hash' => $hashMap[$op]
                ]
            ])
        ], [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->data;
    }
}
