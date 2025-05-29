<?php

namespace Tustin\PlayStation;

use GuzzleHttp\Middleware;
use Tustin\PlayStation\Api;
use GuzzleHttp\HandlerStack;
use Tustin\PlayStation\OAuthToken;
use Tustin\PlayStation\Models\Media;
use Tustin\PlayStation\Factories\Store;
use Tustin\PlayStation\Factories\Users;
use Tustin\PlayStation\Factories\Groups;
use Tustin\PlayStation\Enums\TrophyServiceName;
use Tustin\PlayStation\Factories\CloudMediaGallery;
use Tustin\PlayStation\Models\Trophy\TrophyTitle;
use Tustin\PlayStation\Http\Middleware\AuthenticationMiddleware;
use Tustin\PlayStation\Http\Middleware\ResponseHandlerMiddleware;

class Client extends Api
{
    const AUTH_URL = 'https://ca.account.sony.com/api/';
    const BASE_URL = 'https://m.np.playstation.com/api/';

    private ?OAuthToken $accessToken = null;

    private ?OAuthToken $refreshToken = null;

    private static $instance = null;

    public function __construct(array $guzzleOptions = [])
    {
        $guzzleOptions['allow_redirects'] = false;
        $guzzleOptions['headers']['User-Agent'] = 'PlayStationApp-Android/25.5.2';
        $guzzleOptions['headers']['Accept-Language'] = 'en-US';
        $guzzleOptions['base_uri'] = self::BASE_URL;

        $handlerStack = HandlerStack::create();

        // Push a response handler for handling HTTP errors.
        $handlerStack->push(
            Middleware::mapResponse(
                new ResponseHandlerMiddleware
            )
        );

        // Push a reqeust middleware to inject an Authorization header with the current access token if it exists.
        $handlerStack->push(
            Middleware::mapRequest(
                new AuthenticationMiddleware($this)
            )
        );

        $guzzleOptions['handler'] = $handlerStack;

        parent::__construct(new \GuzzleHttp\Client(
            $guzzleOptions
        ));

        static::$instance = $this;
    }

    public static function create(array $guzzleOptions = []): static
    {
        return static::$instance ?? new static($guzzleOptions);
    }

    public static function getInstance(): static
    {
        return static::$instance ?? new static();
    }

    /**
     * Login with an NPSSO token.
     * 
     * @see https://tustin.dev/psn-php/#/authorization?id=first-login
     */
    public function loginWithNpsso(string $npsso): OAuthToken
    {
        // With the PS App revamp, we now need a JWT token.
        // @TODO: Clean up these params.
        $response = $this->get(self::AUTH_URL . 'authz/v3/oauth/authorize', [
            'access_type' => 'offline',
            'app_context' => 'inapp_ios',
            'auth_ver' => 'v3',
            'cid' => '60351282-8C5F-4D5E-9033-E48FEA973E11',
            'client_id' => '09515159-7237-4370-9b40-3806e67c0891',
            'darkmode' => 'true',
            'device_base_font_size' => 10,
            'device_profile' => 'mobile',
            'duid' => '0000000d0004008088347AA0C79542D3B656EBB51CE3EBE1',
            'elements_visibility' => 'no_aclink',
            'extraQueryParams' => '{
                PlatformPrivacyWs1 = minimal;
            }',
            'no_captcha' => 'true',
            'redirect_uri' => 'com.scee.psxandroid.scecompcall://redirect',
            'response_type' => 'code',
            'scope' => 'psn:mobile.v2.core psn:clientapp',
            'service_entity' => 'urn:service-entity:psn',
            'service_logo' => 'ps',
            'smcid' => 'psapp:settings-entrance',
            'support_scheme' => 'sneiprls',
            'token_format' => 'jwt',
            'ui' => 'pr',
        ], [
            'Cookie' => 'npsso=' . $npsso
        ]);

        $lastResponse = $this->getLastResponse();

        if ($lastResponse->getStatusCode() !== 302) {
            throw new \Exception('Incorrect response code from oauth/authorize.');
        }

        $location = $lastResponse->getHeaderLine('Location');

        if (!$location) {
            throw new \Exception('Missing redirect location from oauth/authorize.');
        }

        $parsedUrl = parse_url($location, PHP_URL_QUERY);

        if ($parsedUrl === null) {
            throw new \Exception('Failed parsing location header');
        }

        parse_str($parsedUrl, $params);

        if (!array_key_exists('code', $params)) {
            throw new \Exception('Missing code from oauth/authorize.');
        }

        $response = $this->post(self::AUTH_URL . 'authz/v3/oauth/token', [
            'smcid' => 'psapp%3Asettings-entrance',
            'access_type' => 'offline',
            'code' => $params['code'],
            'service_logo' => 'ps',
            'ui' => 'pr',
            'elements_visibility' => 'no_aclink',
            'redirect_uri' => 'com.scee.psxandroid.scecompcall://redirect',
            'support_scheme' => 'sneiprls',
            'grant_type' => 'authorization_code',
            'darkmode' => 'true',
            'device_base_font_size' => 10,
            'device_profile' => 'mobile',
            'app_context' => 'inapp_ios',
            'extraQueryParams' => '{
                PlatformPrivacyWs1 = minimal;
            }',
            'token_format' => 'jwt'
        ], [
            'Cookie' => 'npsso=' . $npsso,
            'Authorization' => 'Basic MDk1MTUxNTktNzIzNy00MzcwLTliNDAtMzgwNmU2N2MwODkxOnVjUGprYTV0bnRCMktxc1A=',
        ]);

        return $this->finalizeLogin($response);
    }

    /**
     * Login with an existing refresh token.
     * 
     * @see https://tustin.dev/psn-php/#/authorization?id=future-logins
     */
    public function loginWithRefreshToken(string $refreshToken): OAuthToken
    {
        // @TODO: Handle errors.
        $response = $this->post('authz/v3/oauth/token', [
            'scope' => 'psn:mobile.v2.core psn:clientapp',
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token',
            'token_format' => 'jwt',
        ], ['Authorization' => 'Basic MDk1MTUxNTktNzIzNy00MzcwLTliNDAtMzgwNmU2N2MwODkxOnVjUGprYTV0bnRCMktxc1A=']);

        return $this->finalizeLogin($response);
    }

    /**
     * Finalizes the login flow and sets up future request middleware.
     */
    private function finalizeLogin(object $response): OAuthToken
    {
        $this->accessToken = new OAuthToken($response->access_token, $response->expires_in);
        $this->refreshToken = new OAuthToken($response->refresh_token, $response->refresh_token_expires_in);

        return $this->accessToken;
    }

    /**
     * Access the PlayStation API using an existing access token.
     */
    public function setAccessToken(string $accessToken)
    {
        $this->accessToken = new OAuthToken($accessToken);
    }

    /**
     * Gets the current access token information.
     */
    public function getAccessToken(): ?OAuthToken
    {
        return $this->accessToken;
    }

    /**
     * Gets the current refresh token information.
     */
    public function getRefreshToken(): ?OAuthToken
    {
        return $this->refreshToken;
    }

    /**
     * Get a user from the API.
     */
    public function users(): Users
    {
        return new Users;
    }

    /**
     * Get trophy title information using an NP Communation ID (NPWRxxxxx_00).
     */
    public function trophies(string $npCommunicationId, TrophyServiceName $serviceName = TrophyServiceName::Trophy): TrophyTitle
    {
        return new TrophyTitle($npCommunicationId, $serviceName);
    }

    /**
     * Searches the PlayStation Store.
     */
    public function store(string $query = '')
    {
        return Store::search($query);
    }

    /**
     * Creates a group factory to query your chat groups (parties and text message groups).
     */
    public function groups(): Groups
    {
        return new Groups;
    }

    /**
     * Get a media object from the API.
     */
    public function media(string $ugcId): Media
    {
        return new Media($ugcId);
    }

    /**
     * Gets the cloud media gallery for the user.
     */
    public function cloudMediaGallery(): CloudMediaGallery
    {
        return new CloudMediaGallery;;
    }
}
