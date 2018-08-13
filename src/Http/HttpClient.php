<?php

namespace PlayStation\Http;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Message\Request;
use GuzzleHttp\Message\Response;

class HttpClient {

    private $client;

    public function __construct(ClientInterface $client = null)
    {
        $this->client = $client ?? new GuzzleClient(['verify' => false, 'proxy' => '127.0.0.1:8888']);
    }

    public function get(string $path, array $body = [], array $headers = []) 
    {
        $response = $this->request('GET', $path, $body, false, $headers);

        return ResponseParser::parse($response);
    }

    public function post(string $path, $body, bool $json = false, array $headers = []) 
    {
        $response = $this->request('POST', $path, $body, $json, $headers);

        return ResponseParser::parse($response);
    }

    public function delete(string $path, array $headers = [])
    {
        $response = $this->request('DELETE', $path, null, false, $headers);

        return ResponseParser::parse($response);
    }

    public function patch(string $path, $body = null, bool $json = false, array $headers = [])
    {
        $response = $this->request('PATCH', $path, $body, $json, $headers);

        return ResponseParser::parse($response);
    }

    public function put(string $path, $body = null, bool $json = false, array $headers = [])
    {
        $response = $this->request('PUT', $path, $body, $json, $headers);

        return ResponseParser::parse($response);
    }

    private function request(string $method, string $path, $body = null, bool $json = false, array $headers = []) 
    {
        $options = [];

        if ($method === 'GET' && $body != null) {
            $path .= (strpos($path, '?') === false) ? '?' : '&';
            $path .= urldecode(http_build_query($body));
        } else {
            if ($json) {
                $options["json"] = $body;
            } else {
                $options["form_params"] = $body;
            }
        }

        try {
            return $this->client->request($method, $path, $options);
        } catch (GuzzleException $e) {
            throw $e;
        } 
    }
}