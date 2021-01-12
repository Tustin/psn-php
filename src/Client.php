<?php

namespace Tustin\PlayStation;

use Tustin\Haste\AbstractClient;
use Tustin\PlayStation\OAuthToken;

use Tustin\PlayStation\Model\TrophyTitle;
use Tustin\PlayStation\Factory\UsersFactory;
use Tustin\PlayStation\Factory\TrophyGroupsFactory;
use Tustin\PlayStation\Factory\TrophyTitlesFactory;
use Tustin\Haste\Http\Middleware\AuthenticationMiddleware;

class Client extends AbstractClient
{
    private const VERSION = 'dev-3.0.0';

    const AUTH_URL = 'https://ca.account.sony.com/api/';
    const BASE_URL = 'https://m.np.playstation.net/api/';

    private $options;

    private $accessToken;
    private $refreshToken;

    public function __construct(array $guzzleOptions = [])
    {
        $guzzleOptions['allow_redirects'] = false;
        $guzzleOptions['headers']['User-Agent'] = 'psn-php/' . self::VERSION;
        $guzzleOptions['base_uri'] = self::BASE_URL;

        parent::__construct($guzzleOptions);
    }

    /**
     * Login with an NPSSO token.
     * 
     * @see https://tusticles.com/psn-php/first_login.html
     *
     * @param string $npsso
     * @return void
     */
    public function loginWithNpsso(string $npsso)
    {
        // With the PS App revamp, we now need a JWT token.
        // @TODO: Clean up these params.
        $response = $this->get(self::AUTH_URL . 'authz/v3/oauth/authorize', [
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

        parse_str(parse_url($location, PHP_URL_QUERY), $params);

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
            'token_format' => 'jwt'
        ], [
            'Cookie' => 'npsso=' . $npsso,
            'Authorization' => 'Basic YWM4ZDE2MWEtZDk2Ni00NzI4LWIwZWEtZmZlYzIyZjY5ZWRjOkRFaXhFcVhYQ2RYZHdqMHY=',
        ]);

        $this->finalizeLogin($response);
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
        $response = $this->getHttpClient()->post('authz/v3/oauth/token', [
            'scope' => 'psn:mobile.v1 psn:clientapp',
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token',
            'token_format' => 'jwt',
        ], ['Authorization' => 'Basic YWM4ZDE2MWEtZDk2Ni00NzI4LWIwZWEtZmZlYzIyZjY5ZWRjOkRFaXhFcVhYQ2RYZHdqMHY=']);

        $this->finalizeLogin($response);
    }

    /**
     * Finishes the login flow and sets up future request middleware.
     *
     * @param object $response
     * @return void
     */
    private function finalizeLogin(object $response)
    {
        $this->accessToken = new OAuthToken($response->access_token, $response->expires_in);
        $this->refreshToken = new OAuthToken($response->refresh_token, $response->refresh_token_expires_in);

        $this->pushAuthenticationMiddleware(new AuthenticationMiddleware([
            'Authorization' => 'Bearer ' . $this->getAccessToken()->getToken(),
        ]));
    }

    /**
     * Access the PlayStation API using an existing access token.
     *
     * @param string $accessToken
     * @return void
     */
    public function setAccessToken(string $accessToken)
    {
        $this->pushAuthenticationMiddleware(new AuthenticationMiddleware([
            'Authorization' => 'Bearer ' . $accessToken
        ]));
    }

    /**
     * Gets the access token.
     *
     * @return OAuthToken
     */
    public function getAccessToken() : OAuthToken
    {
        return $this->accessToken;
    }

    /**
     * Gets the refresh token.
     *
     * @return OAuthToken
     */
    public function getRefreshToken() : OAuthToken
    {
        return $this->refreshToken;
    }

    /**
     * Creates a UsersFactory to query user information.
     *
     * @return UsersFactory
     */
    public function users() : UsersFactory
    {
        return new UsersFactory($this->getHttpClient());
	}
	
	public function trophies(string $npCommunicationId)
	{
		$title = new TrophyTitle($this->getHttpClient());
		$title->setNpCommuncationId($npCommunicationId);

		return $title;
	}
}