<?php

namespace Tustin\PlayStation\Exceptions;

use Psr\Http\Message\StreamInterface;

class ApiException extends \Exception
{
    /**
     * @param StreamInterface $stream
     */
    public function __construct($stream)
    {
        // @TODO
    }
}
