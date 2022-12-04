<?php

namespace PlayStation\Http;

use GuzzleHttp\Psr7\Response;

class ResponseParser {

    /**
     * Parses a GuzzleHttp Response.
     *
     * Will return one of three types of values:
     * 1. JSON - will attempt to parse the response as JSON first.
     * 2. String - if JSON was invalid and there is a response body, it will just return it as a string.
     * 3. Response - if the JSON failed and the response body was empty, the entire Response object will be returned.
     * 
     * @param Response $response
     * @return mixed
     */
    public static function parse(Response $response): mixed
    {
        $contents = $response->getBody()->getContents();
        
        $data = json_decode($contents);

        return (json_last_error() === JSON_ERROR_NONE) ? $data : (empty($contents) ? $response : $contents);
    }
}