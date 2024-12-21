<?php

namespace Tustin\PlayStation\Interfaces;

use Tustin\PlayStation\Client;

interface FactoryInterface
{
    public function __construct(Client $client);
}
