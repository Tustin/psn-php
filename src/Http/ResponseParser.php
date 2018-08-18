<?php

namespace PlayStation\Http;

use GuzzleHttp\Psr7\Response;

class ResponseParser {

    public static function parse(Response $response) 
    {
        $contents = $response->getBody()->getContents();
        
        $data = json_decode($contents);

        return (json_last_error() === JSON_ERROR_NONE) ? $data : (empty($contents) ? $response : $contents);
    }
}