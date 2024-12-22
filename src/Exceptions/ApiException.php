<?php

namespace Tustin\PlayStation\Exceptions;

use Psr\Http\Message\StreamInterface;
use Tustin\PlayStation\Http\JsonStream;

class ApiException extends \Exception
{
    public function __construct(JsonStream $stream)
    {
        $serialized = $stream->jsonSerialize();

        if ($serialized) {
            parent::__construct(
                $serialized?->error?->message ?? 'Unknown error',
                $serialized?->error?->code ?? 0
            );
        }
    }
}
