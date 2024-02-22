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

class Client
{
    public static string $apiBaseUrl = 'https://m.np.playstation.com/api';

    public static string $userAgent = 'PlayStation/21090100 CFNetwork/1126 Darwin/19.5.0';

    public static string $acceptLanguage = 'en-US';

    public static ?string $npsso = null;

    public static ?OAuthToken $accessToken = null;

    public static ?OAuthToken $refreshToken = null;

    public static array $guzzleOptions = [];

    /**
     * Get the NPSSO used for the account making authenticated requests.
     */
    public static function getNpsso(): ?string
    {
        return self::$npsso;
    }

    /**
     * Set the NPSSO used for the account making authenticated requests.
     * 
     * @todo Update this url to the new one.
     * @see https://tusticles.com/psn-php/first_login.html
     */
    public static function setNpsso(string $npsso): void
    {
        self::$npsso = $npsso;
    }

    /**
     * Set the access token for all requests.
     */
    public static function setAccessToken(string $accessToken): void
    {
        \Tustin\PlayStation\OAuthToken::$accessToken = $accessToken;
    }

    /**
     * Set the Guzzle options for all requests.
     */
    public static function setGuzzleOptions(array $options): void
    {
        self::$guzzleOptions = $options;
    }

    /**
     * Login with an existing refresh token.
     * 
     * @see https://tusticles.com/psn-php/future_logins.html
     *
     * @param string $refreshToken
     * @return void
     */
    public function loginWithRefreshToken(string $refreshToken)
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
     * Creates a UsersFactory to query user information.
     *
     * @return UsersFactory
     */
    public function users(): UsersFactory
    {
        return new UsersFactory($this->getHttpClient());
    }

    /**
     * Gets a trophy title from the API using a communication id (NPWRxxxxx_00).
     *
     * @param string $npCommunicationId
     * @param string $serviceName
     * @return TrophyTitle
     */
    public function trophies(string $npCommunicationId, string $serviceName = 'trophy'): TrophyTitle
    {
        return new TrophyTitle($this->getHttpClient(), $npCommunicationId, $serviceName);
    }

    /**
     * Creates a store factory to navigate the PlayStation Store.
     *
     * @return StoreFactory
     */
    public function store(): StoreFactory
    {
        return new StoreFactory($this->getHttpClient());
    }

    /**
     * Creates a group factory to query your chat groups (parties and text message groups).
     *
     * @return GroupsFactory
     */
    public function groups(): GroupsFactory
    {
        return new GroupsFactory($this->getHttpClient());
    }

    /**
     * Get a media object from the API.
     *
     * @param string $ugcId
     * @return Media
     */
    public function media(string $ugcId): Media
    {
        return new Media($this->getHttpClient(), $ugcId);
    }

    /**
     * Gets the cloud media gallery for the user.
     *
     * @return CloudMediaGalleryFactory
     */
    public function cloudMediaGallery(): CloudMediaGalleryFactory
    {
        return new CloudMediaGalleryFactory($this->getHttpClient());
    }
}
