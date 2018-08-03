<?php

namespace PlayStation\Api;

use PlayStation\Client;
use PlayStation\Http\HttpClient;
use PlayStation\Http\ResponseParser;

abstract class AbstractApi {

    protected $client;

    public function __construct(Client $client) 
    {
        $this->client = $client;
    }

    public function get(string $path, array $parameters = [], array $headers = []) 
    {
        $response = $this->client->getHttpClient()->get($path, $parameters, $headers);

        return ResponseParser::parse($response);
    }

    public function post(string $path, array $parameters = [], array $headers = []) 
    {
        $response = $this->client->getHttpClient()->post($path, $parameters, $headers);

        return ResponseParser::parse($response);
    }

}