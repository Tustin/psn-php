<?php

namespace Tustin\PlayStation;

class OAuthToken
{
    private string $token;

    private ?\DateTime $expiration;

    private ?int $seconds;

    public function __construct(string $token, ?int $expiresIn = null)
    {
        if ($expiresIn !== null && $expiresIn < 0) {
            throw new \InvalidArgumentException('expiresIn has to be an integer > 0');
        }

        $this->token = $token;
        $this->seconds = $expiresIn;
        $this->expiration = \Carbon\Carbon::now()->addSeconds($expiresIn);
    }

    /**
     * Gets the OAuth token.
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * Gets the OAuth token's expiration date and time.
     */
    public function getExpiration(): ?\DateTime
    {
        return $this->expiration;
    }

    /**
     * Gets the OAuth token's expiration in seconds.
     */
    public function getExpirationSeconds(): ?int
    {
        return $this->seconds;
    }
}
