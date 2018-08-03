<?php

namespace PlayStation\Http;

use GuzzleHttp\Psr7\Response;

class ResponseParser {

    public static function parse(Response $response) : object 
    {
        return json_decode($response->getBody()->getContents());
    }

}