<?php

namespace Tustin\PlayStation\Http;

use JsonSerializable;
use RuntimeException;

use Psr\Http\Message\StreamInterface;
use GuzzleHttp\Psr7\StreamDecoratorTrait;

class JsonStream implements StreamInterface, JsonSerializable
{
    use StreamDecoratorTrait;

    public StreamInterface $stream;

    /**
     * Serializes a response body to JSON.
     * 
     * @throws RuntimeException
     */
    public function jsonSerialize(): ?object
    {
        $contents = (string) $this->getContents();

        if ($contents === '') {
            return null;
        }

        $decoded = json_decode($contents, false);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException(
                'Error trying to decode response: ' .
                    json_last_error_msg()
            );
        }

        return $decoded;
    }
}
