<?php

namespace Tustin\PlayStation;

class ApiResponse
{
    public function __construct(public array $headers = [], public string $body = '', public int $statusCode = 200)
    {
        $this->headers = $headers;
        $this->body = $body;
        $this->statusCode = $statusCode;
    }

    /**
     * Gets the HTTP response headers as an associative array.
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Gets the HTTP response body as a string.
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * Gets the HTTP response status code.
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Gets the HTTP response header line.
     */
    public function getHeaderLine(string $header): string
    {
        return $this->headers[$header][0];
    }

    /**
     * Get the response body as a JSON array.
     */
    public function json(?string $key = null): array
    {
        $decoded = json_decode($this->body, associative: true);

        if ($key) {
            return $decoded[$key];
        }

        return $decoded;
    }
}
