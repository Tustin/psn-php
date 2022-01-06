<?php

namespace Tests;

use Carbon\Carbon;
use Tustin\PlayStation\OAuthToken;
use PHPUnit\Framework\TestCase;

class OAuthTokenTest extends TestCase
{

    public function testItShouldSetExpiration(): void
    {
        // This is hard to test as the Carbon object is not injected into the class.
        // Ideally we would inject a clock VO, that allows us to use a "Paused" clock in unit tests.
        $oAuthToken = new OAuthToken('some-token', 60);
        $this->assertEquals(Carbon::now()->addSeconds(60)->format('Y-m-d H:i:s'), $oAuthToken->getExpiration()->format('Y-m-d H:i:s'));
        $oAuthToken = new OAuthToken('some-token', 333);
        $this->assertEquals(Carbon::now()->addSeconds(333)->format('Y-m-d H:i:s'), $oAuthToken->getExpiration()->format('Y-m-d H:i:s'));
    }

    public function testItShouldThrow(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('expiresIn has to be an integer > 0');
        new OAuthToken('some-token', -200);
    }
}
