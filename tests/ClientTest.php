<?php

namespace Tests;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Tustin\Haste\Http\JsonStream;
use Tustin\PlayStation\Client;
use PHPUnit\Framework\TestCase;
use Tustin\PlayStation\Factory\CloudMediaGalleryFactory;
use Tustin\PlayStation\Factory\GroupsFactory;
use Tustin\PlayStation\Factory\StoreFactory;
use Tustin\PlayStation\Factory\UsersFactory;
use Tustin\PlayStation\Model\Media;
use Tustin\PlayStation\Model\Trophy\TrophyTitle;

class ClientTest extends TestCase
{
    private Client $client;
    private $httpClient;

    public function testItShouldLoginWithNpSso(): void
    {
        $npSso = 'NpSso';
        $authCode = 'AUTH-CODE';
        $authorizeResponse = new Response(302, ['Location' => 'https://some-redirect.com?code=' . $authCode], '{}');
        $tokenResponse = new Response(200, [], '{"access_token": "some-access-token", "expires_in": 60, "refresh_token": "some-refresh-token", "refresh_token_expires_in": 60}');

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with(
                Client::AUTH_URL . 'authz/v3/oauth/authorize',
                [
                    'query' => $this->getAuthorizeQueryParams(),
                    'headers' => [
                        'Cookie' => 'npsso=' . $npSso,
                    ],
                ]
            )
            ->willReturn($authorizeResponse->withBody(new JsonStream($authorizeResponse->getBody())));

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with(
                Client::AUTH_URL . 'authz/v3/oauth/token',
                [
                    'form_params' => $this->getTokenFormParams($authCode),
                    'headers' => [
                        'Cookie' => 'npsso=' . $npSso,
                        'Authorization' => 'Basic YWM4ZDE2MWEtZDk2Ni00NzI4LWIwZWEtZmZlYzIyZjY5ZWRjOkRFaXhFcVhYQ2RYZHdqMHY=',
                    ],
                ]
            )
            ->willReturn($tokenResponse->withBody(new JsonStream($tokenResponse->getBody())));

        $this->httpClient
            ->expects($this->atLeastOnce())
            ->method('getConfig')
            ->willReturn(['handler' => HandlerStack::create()]);

        $this->client->loginWithNpsso($npSso);

        $this->assertEquals('some-access-token', $this->client->getAccessToken()->getToken());
        $this->assertEquals('some-refresh-token', $this->client->getRefreshToken()->getToken());
    }

    public function testItShouldLoginWithRefreshToken(): void
    {
        $refreshToken = 'some-refresh-token';
        $response = new Response(200, [], '{"access_token": "some-access-token", "expires_in": 60, "refresh_token": "some-refresh-token", "refresh_token_expires_in": 60}');

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with(
                'authz/v3/oauth/token',
                [
                    'form_params' => [
                        'scope' => 'psn:mobile.v1 psn:clientapp',
                        'refresh_token' => $refreshToken,
                        'grant_type' => 'refresh_token',
                        'token_format' => 'jwt',
                    ],
                    'headers' => [
                        'Authorization' => 'Basic YWM4ZDE2MWEtZDk2Ni00NzI4LWIwZWEtZmZlYzIyZjY5ZWRjOkRFaXhFcVhYQ2RYZHdqMHY=',
                    ],
                ]
            )
            ->willReturn($response->withBody(new JsonStream($response->getBody())));

        $this->httpClient
            ->expects($this->atLeastOnce())
            ->method('getConfig')
            ->willReturn(['handler' => HandlerStack::create()]);

        $this->client->loginWithRefreshToken($refreshToken);
    }

    public function testItShouldThrowOnInvalidResponseCode(): void
    {
        $npSso = 'NpSso';
        $authCode = 'AUTH-CODE';
        $authorizeResponse = new Response(404, ['Location' => 'https://some-redirect.com?code=' . $authCode], '{}');

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with(
                Client::AUTH_URL . 'authz/v3/oauth/authorize',
                [
                    'query' => $this->getAuthorizeQueryParams(),
                    'headers' => [
                        'Cookie' => 'npsso=' . $npSso,
                    ],
                ]
            )
            ->willReturn($authorizeResponse->withBody(new JsonStream($authorizeResponse->getBody())));

        $this->httpClient
            ->expects($this->never())
            ->method('post');

        $this->httpClient
            ->expects($this->never())
            ->method('getConfig');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Incorrect response code from oauth/authorize.');
        $this->client->loginWithNpsso($npSso);
    }

    public function testItShouldThrowOnEmptyHeaderLocation(): void
    {
        $npSso = 'NpSso';
        $authorizeResponse = new Response(302, [], '{}');

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with(
                Client::AUTH_URL . 'authz/v3/oauth/authorize',
                [
                    'query' => $this->getAuthorizeQueryParams(),
                    'headers' => [
                        'Cookie' => 'npsso=' . $npSso,
                    ],
                ]
            )
            ->willReturn($authorizeResponse->withBody(new JsonStream($authorizeResponse->getBody())));

        $this->httpClient
            ->expects($this->never())
            ->method('post');

        $this->httpClient
            ->expects($this->never())
            ->method('getConfig');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Missing redirect location from oauth/authorize.');
        $this->client->loginWithNpsso($npSso);
    }

    public function testItShouldThrowOnEmptyQueryParam(): void
    {
        $npSso = 'NpSso';
        $authorizeResponse = new Response(302, ['Location' => 'https://some-redirect.com'], '{}');

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with(
                Client::AUTH_URL . 'authz/v3/oauth/authorize',
                [
                    'query' => $this->getAuthorizeQueryParams(),
                    'headers' => [
                        'Cookie' => 'npsso=' . $npSso,
                    ],
                ]
            )
            ->willReturn($authorizeResponse->withBody(new JsonStream($authorizeResponse->getBody())));

        $this->httpClient
            ->expects($this->never())
            ->method('post');

        $this->httpClient
            ->expects($this->never())
            ->method('getConfig');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Missing code from oauth/authorize.');
        $this->client->loginWithNpsso($npSso);
    }

    public function testItShouldReturnFactories(): void
    {
        $this->assertEquals(new UsersFactory($this->httpClient), $this->client->users());
        $this->assertEquals(new TrophyTitle($this->httpClient, 'id', 'trophy'), $this->client->trophies('id'));
        $this->assertEquals(new StoreFactory($this->httpClient), $this->client->store());
        $this->assertEquals(new GroupsFactory($this->httpClient), $this->client->groups());
        $this->assertEquals(new Media($this->httpClient, 'id'), $this->client->media('id'));
        $this->assertEquals(new CloudMediaGalleryFactory($this->httpClient), $this->client->cloudMediaGallery());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = new Client();
        $this->httpClient = $this->createMock(\GuzzleHttp\Client::class);

        // Because our Guzzle client is not injected in to the client,
        // we need to do some magic to make sure we can mock it.
        // Ideally this should be refactored to DI.
        $class = new \ReflectionClass(Client::class);
        $property = $class->getProperty('httpClient');
        $property->setAccessible(true);
        $property->setValue($this->client, $this->httpClient);
    }

    private function getAuthorizeQueryParams(): array
    {
        return [
            'access_type' => 'offline',
            'app_context' => 'inapp_ios',
            'auth_ver' => 'v3',
            'cid' => '60351282-8C5F-4D5E-9033-E48FEA973E11',
            'client_id' => 'ac8d161a-d966-4728-b0ea-ffec22f69edc',
            'darkmode' => 'true',
            'device_base_font_size' => 10,
            'device_profile' => 'mobile',
            'duid' => '0000000d0004008088347AA0C79542D3B656EBB51CE3EBE1',
            'elements_visibility' => 'no_aclink',
            'extraQueryParams' => '{
                PlatformPrivacyWs1 = minimal;
            }',
            'no_captcha' => 'true',
            'redirect_uri' => 'com.playstation.PlayStationApp://redirect',
            'response_type' => 'code',
            'scope' => 'psn:mobile.v1 psn:clientapp',
            'service_entity' => 'urn:service-entity:psn',
            'service_logo' => 'ps',
            'smcid' => 'psapp:settings-entrance',
            'support_scheme' => 'sneiprls',
            'token_format' => 'jwt',
            'ui' => 'pr',
        ];
    }

    private function getTokenFormParams(string $authCode): array
    {
        return [
            'smcid' => 'psapp%3Asettings-entrance',
            'access_type' => 'offline',
            'code' => $authCode,
            'service_logo' => 'ps',
            'ui' => 'pr',
            'elements_visibility' => 'no_aclink',
            'redirect_uri' => 'com.playstation.PlayStationApp://redirect',
            'support_scheme' => 'sneiprls',
            'grant_type' => 'authorization_code',
            'darkmode' => 'true',
            'device_base_font_size' => 10,
            'device_profile' => 'mobile',
            'app_context' => 'inapp_ios',
            'extraQueryParams' => '{
                PlatformPrivacyWs1 = minimal;
            }',
            'token_format' => 'jwt',
        ];
    }
}
