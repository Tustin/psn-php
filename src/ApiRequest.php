<?php

namespace Tustin\PlayStation;

class ApiRequest
{
    private ?\GuzzleHttp\Client $client = null;

    public array $guzzleOptions = [];

    public ?ApiResponse $lastResponse = null;

    public function __construct(public ?string $accessToken = null, public ?string $apiBaseUrl = null)
    {
        $this->guzzleOptions['allow_redirects'] = false;
        $this->guzzleOptions['headers']['User-Agent'] = Client::$userAgent;
        $this->guzzleOptions['headers']['Accept-Language'] = Client::$acceptLanguage;
        $this->guzzleOptions['base_uri'] = $apiBaseUrl ?? Client::$apiBaseUrl;

        if ($accessToken !== null) {
            $this->guzzleOptions['headers']['Authorization'] = 'Bearer ' . $accessToken;
        }

        $this->guzzleOptions = array_merge($this->guzzleOptions, Client::$guzzleOptions);
    }

    /**
     * Gets the Guzzle HTTP client.
     */
    public function getHttpClient(): \GuzzleHttp\Client
    {
        if ($this->client === null) {
            $this->client = new \GuzzleHttp\Client($this->guzzleOptions);
        }

        return $this->client;
    }

    /**
     * Sends a request to the API.
     */
    public function request(
        string $method,
        string $uri,
        array $options = []
    ): ApiResponse {

        $response = $this->getHttpClient()->request($method, $uri, $options);

        $this->lastResponse = new ApiResponse(
            headers: $response->getHeaders(),
            body: $response->getBody()->getContents(),
            statusCode: $response->getStatusCode()
        );

        return $this->lastResponse;
    }

    /**
     * Sends a GET request to the API.
     */
    public function get(string $uri, array $query = [], array $headers = []): ApiResponse
    {
        return $this->request('GET', $uri, [
            'query' => $query,
            'headers' => $headers
        ]);
    }

    /**
     * Sends a POST request to the API with JSON data.
     */
    public function post(string $uri, array $body = [], array $headers = []): ApiResponse
    {
        return $this->request('POST', $uri, [
            'json' => $body,
            'headers' => $headers
        ]);
    }

    /**
     * Sends a POST request to the API with form data.
     */
    public function postForm(string $uri, array $body = [], array $headers = []): ApiResponse
    {
        return $this->request('POST', $uri, [
            'form_params' => $body,
            'headers' => $headers
        ]);
    }
}
