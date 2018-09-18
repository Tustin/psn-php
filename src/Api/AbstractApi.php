<?php

namespace PlayStation\Api;

use PlayStation\Client;
use PlayStation\Http\HttpClient;
use PlayStation\Http\ResponseParser;

abstract class AbstractApi 
{
    protected $client;

    public function __construct(Client $client) 
    {
        $this->client = $client;
    }

    public function get(string $path, array $parameters = [], array $headers = []) 
    {
        return $this->client->httpClient()->get($path, $parameters, $headers);
    }

    public function post(string $path, $parameters, array $headers = []) 
    {
        return $this->client->httpClient()->post($path, $parameters, HttpClient::FORM, $headers);
    }

    public function postJson(string $path, $parameters, array $headers = []) 
    {
        return $this->client->httpClient()->post($path, $parameters, HttpClient::JSON, $headers);
    }

    public function postMultiPart(string $path, array $parameters, array $headers = [])
    {
        return $this->client->httpClient()->post($path, $parameters, HttpClient::MULTI, $headers);
    }

    public function delete(string $path, array $headers = []) 
    {
        return $this->client->httpClient()->delete($path, $headers);
    }

    public function patch(string $path, $parameters, array $headers = [])
    {
       return $this->client->httpClient()->patch($path, $parameters, HttpClient::FORM, $headers);
    }

    public function patchJson(string $path, $parameters, array $headers = [])
    {
        return $this->client->httpClient()->patch($path, $parameters, HttpClient::JSON, $headers);
    }

    public function put(string $path, $parameters, array $headers = [])
    {
        return $this->client->httpClient()->put($path, $parameters, HttpClient::FORM, $headers);
    }

    public function putJson(string $path, $parameters, array $headers = [])
    {
        return $this->client->httpClient()->put($path, $parameters,  HttpClient::JSON, $headers);       
    }

    public function putMultiPart(string $path, array $parameters, array $headers = [])
    {
        return $this->client->httpClient()->put($path, $parameters, HttpClient::MULTI, $headers);
    }
}