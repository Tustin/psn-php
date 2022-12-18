<?php

namespace Tustin\PlayStation;

use Tustin\Haste\AbstractClient;
use Tustin\PlayStation\OAuthToken;
use Tustin\PlayStation\Model\Media;
use Tustin\PlayStation\Factory\StoreFactory;
use Tustin\PlayStation\Factory\UsersFactory;
use Tustin\PlayStation\Factory\GroupsFactory;
use Tustin\PlayStation\Model\Trophy\TrophyTitle;
use Tustin\PlayStation\Factory\CloudMediaGalleryFactory;
use Tustin\Haste\Http\Middleware\AuthenticationMiddleware;
use Tustin\PlayStation\Model\Store\Concept;
use Tustin\PlayStation\Model\User;

class Client extends AbstractClient
{
    const AUTH_URL = 'https://ca.account.sony.com/api/';
    const BASE_URL = 'https://m.np.playstation.com/api/';

    /**
     * The current access token.
     */
    private OAuthToken $accessToken;

    /**
     * The current refresh token.
     */
    private OAuthToken $refreshToken;

    public function __construct(array $guzzleOptions = [])
    {
        $guzzleOptions['allow_redirects'] = false;
        $guzzleOptions['headers']['User-Agent'] = 'PlayStation/21090100 CFNetwork/1126 Darwin/19.5.0';
        $guzzleOptions['headers']['Accept-Language'] = 'en-US';
        $guzzleOptions['base_uri'] = self::BASE_URL;

        parent::__construct($guzzleOptions);
    }

    /**
     * Login with an NPSSO token.
     * 
     * @see https://tustin.dev/psn-php/#/authorization?id=first-login
     */
    public function loginWithNpsso(string $npsso): void
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

        $this->finalizeLogin($response);
    }

    /**
     * Login with an existing refresh token.
     * 
     * @see https://tustin.dev/psn-php/#/authorization?id=future-logins
     */
    public function loginWithRefreshToken(string $refreshToken): void
    {
        // @TODO: Handle errors.
        $response = $this->post('authz/v3/oauth/token', [
            'scope' => 'psn:mobile.v2.core psn:clientapp',
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token',
            'token_format' => 'jwt',
        ], ['Authorization' => 'Basic MDk1MTUxNTktNzIzNy00MzcwLTliNDAtMzgwNmU2N2MwODkxOnVjUGprYTV0bnRCMktxc1A=']);

        $this->finalizeLogin($response);
    }

    /**
     * Finishes the login flow and sets up future request middleware.
     */
    private function finalizeLogin(object $response): void
    {
        $this->accessToken = new OAuthToken($response->access_token, $response->expires_in);
        $this->refreshToken = new OAuthToken($response->refresh_token, $response->refresh_token_expires_in);

        $this->pushAuthenticationMiddleware(new AuthenticationMiddleware([
            'Authorization' => 'Bearer ' . $this->getAccessToken()->getToken(),
        ]));
    }

    /**
     * Sets the access token.
     */
    public function setAccessToken(string $accessToken): void
    {
        $this->pushAuthenticationMiddleware(new AuthenticationMiddleware([
            'Authorization' => 'Bearer ' . $accessToken
        ]));
    }

    /**
     * Gets the access token.
     */
    public function getAccessToken(): OAuthToken
    {
        return $this->accessToken;
    }

    /**
     * Gets the refresh token.
     */
    public function getRefreshToken(): OAuthToken
    {
        return $this->refreshToken;
    }

    /**
     * Creates a UsersFactory to query user information.
     */
    public function users(): UsersFactory
    {
        return new UsersFactory($this->getHttpClient());
    }

    /**
     * Gets a user from the API using an account id.
     * 
     * Account ids can be found by searching for a user using the `users` method.
     */
    public function user(string $accountId): User
    {
        return new User($this->getHttpClient(), $accountId);
    }

    /**
     * Gets a store concept from the API using a concept id.
     */
    public function concept(int $conceptId): Concept
    {
        return new Concept($this->getHttpClient(), $conceptId);
    }

    /**
     * Gets a trophy title from the API using a communication id (NPWRxxxxx_00).
     * 
     * Service name is usually 'trophy' but can be 'trophy2' for some titles.
     */
    public function trophyTitle(string $npCommunicationId, string $serviceName = 'trophy'): TrophyTitle
    {
        return new TrophyTitle($this->getHttpClient(), $npCommunicationId, $serviceName);
    }

    /**
     * Gets a trophy title from the API using a communication id (NPWRxxxxx_00).
     * 
     * @deprecated Use trophyTitle() instead.
     */
    public function trophies(string $npCommunicationId, string $serviceName = 'trophy'): TrophyTitle
    {
        return $this->trophyTitle($npCommunicationId, $serviceName);
    }

    /**
     * Creates a store factory to navigate the PlayStation Store.
     */
    public function store(): StoreFactory
    {
        return new StoreFactory($this->getHttpClient());
    }

    /**
     * Creates a group factory to query your chat groups (parties and text message groups).
     */
    public function groups(): GroupsFactory
    {
        return new GroupsFactory($this->getHttpClient());
    }

    /**
     * Get a media object from the API.
     */
    public function media(string $ugcId): Media
    {
        return new Media($this->getHttpClient(), $ugcId);
    }

    /**
     * Gets the cloud media gallery for the user.
     */
    public function cloudMediaGallery(): CloudMediaGalleryFactory
    {
        return new CloudMediaGalleryFactory($this->getHttpClient());
    }
}
