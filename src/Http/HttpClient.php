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
        try {
            return $this->request('GET', $path, $body, false, $headers);
        } catch (\Exception $ex) {
        }
    }

    public function post(string $path, $body, bool $json = false, array $headers = []) 
    {
        try {
            return $this->request('POST', $path, $body, $json, $headers);
        } catch (\Exception $ex) {

        }
    }

    public function delete(string $path, array $headers = [])
    {
        try {
            return $this->request('DELETE', $path, null, false, $headers);
        } catch (\Exception $ex) {
            
        }
    }

    public function patch(string $path, $body = null, bool $json = false, array $headers = [])
    {
        try {
            return $this->request('PATCH', $path, $body, $json, $headers);
        } catch (\Exception $ex) {
            
        }
    }

    public function put(string $path, $body = null, bool $json = false, array $headers = [])
    {
        try {
            return $this->request('PUT', $path, $body, $json, $headers);
        } catch (\Exception $ex) {
            
        }
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
            var_dump($response->getBody()->getContents());
        } 
    }
}