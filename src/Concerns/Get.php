<?php

namespace Tustin\PlayStation\Concerns;

/**
 * Retrieves a specific record from an API endpoint.
 */
trait Get
{
    public static function get(string $id): static
    {
        $instance = new static($id);

        $instance->refresh();

        return $instance;
    }
}
