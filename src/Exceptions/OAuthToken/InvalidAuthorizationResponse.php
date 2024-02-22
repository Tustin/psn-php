<?php

namespace Tustin\PlayStation\Exceptions\OAuthToken;

class InvalidAuthorizationResponse extends \Exception
{
    public function __construct(?string $message = null)
    {
        parent::__construct($message);
    }
}
