<?php

namespace PlayStation\Tests;

use PlayStation\Client;

class AuthenticationTest extends \PHPUnit\Framework\TestCase
{

    /**
     * PlayStation Client
     *
     * @var PlayStation\Client;
     */
    protected $client;

    protected function setUp()
    {
        $this->client = new Client();
    }

    public function testEnvironment()
    {
        $refreshToken = getenv('PSN_PHP_REFRESH_TOKEN');
        $this->assertNotEmpty($refreshToken, 'Missing refresh token for PSN API.');
    }

    public function testInvalidRefreshToken()
    {
        $this->expectException('\GuzzleHttp\Exception\ClientException');
        $this->client->login('deadbeef');
    }

    public function testInvalidTwoFactorLogin()
    {
        $this->expectException('\GuzzleHttp\Exception\ClientException');
        $this->client->login('abc', 6969);
    }

    public function testLoginWithRefreshToken()
    {
        $refreshToken = getenv('PSN_PHP_REFRESH_TOKEN');
        
        if (!$refreshToken)
        {
            $this->markTestsSkipped('Missing refresh token in environment.');
        }

        $this->client->login($refreshToken);

        $this->assertEquals($this->client->onlineId(), 'speedy424key');
    }
}