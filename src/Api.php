<?php

namespace Tustin\PlayStation;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Tustin\PlayStation\Enums\GraphqlOperation;
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

    public function graphql(GraphqlOperation $operation, array $variables): object
    {
        return $this->get('graphql/v1/op', [
            'operationName' => $operation->value,
            'variables' => json_encode($variables),
            'extensions' => json_encode([
                'persistedQuery' => [
                    'version' => 1,
                    'sha256Hash' => $operation->hash()
                ]
            ])
        ], [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ])->data;
    }
}
