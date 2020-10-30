<?php
namespace Tustin\PlayStation\Api;

use GuzzleHttp\Client;

use Tustin\Haste\Http\HttpClient;

class Api extends HttpClient
{
    public function __construct(Client $client)
    {
        $this->httpClient = $client;
    }
}