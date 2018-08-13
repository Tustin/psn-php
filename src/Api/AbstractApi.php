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
        return $this->client->getHttpClient()->get($path, $parameters, $headers);
    }

    public function post(string $path, $parameters, array $headers = []) 
    {
        return $this->client->getHttpClient()->post($path, $parameters, false, $headers);
    }

    public function postJson(string $path, $parameters, array $headers = []) 
    {
        return $this->client->getHttpClient()->post($path, $parameters, true, $headers);
    }

    public function delete(string $path, array $headers = []) 
    {
        return $this->client->getHttpClient()->delete($path, $headers);
    }

    public function patch(string $path, $parameters, array $headers = [])
    {
       return $this->client->getHttpClient()->patch($path, $parameters, false, $headers);
    }

    public function patchJson(string $path, $parameters, array $headers = [])
    {
        return $this->client->getHttpClient()->patch($path, $parameters, true, $headers);
    }

    public function put(string $path, $parameters, array $headers = [])
    {
        return $this->client->getHttpClient()->put($path, $parameters, false, $headers);
    }

    public function putJson(string $path, $parameters, array $headers = [])
    {
        return $this->client->getHttpClient()->put($path, $parameters, true, $headers);       
    }

}