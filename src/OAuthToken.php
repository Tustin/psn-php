<?php

namespace Tustin\PlayStation;

use Tustin\PlayStation\Api;
use Tustin\PlayStation\Exceptions\OAuthToken\InvalidAuthorizationResponse;

class OAuthToken extends Api
{
    public static ?string $accessToken = null;

    public static ?string $refreshToken = null;

    /**
     * Gets the access token for the current session.
     * This will automatically authorize the user if they haven't been authorized yet.
     */
    public static function accessToken(): string
    {
        if (static::$accessToken !== null) {
            return static::$accessToken;
        }

        $request = new ApiRequest(
            apiBaseUrl: static::getBaseUrl(),
        );

        $response = $request->get('authorize', [
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
        ], ['Cookie' => 'npsso=' . Client::$npsso]);

        if ($response->getStatusCode() !== 302) {
            throw new InvalidAuthorizationResponse(
                'Incorrect response code from oauth/authorize.'
            );
        }

        $location = $response->getHeaderLine('Location');

        if (!$location) {
            throw new InvalidAuthorizationResponse(
                'Missing location header from oauth/authorize.'
            );
        }

        $parsedUrl = parse_url($location, PHP_URL_QUERY);

        if ($parsedUrl === null) {
            throw new InvalidAuthorizationResponse(
                'Failed parsing location header.'
            );
        }

        parse_str($parsedUrl, $params);

        if (array_key_exists('error', $params)) {
            throw new InvalidAuthorizationResponse(
                'Error from oauth/authorize: ' . $params['error_description'] ?? $params['error']
            );
        }

        if (!array_key_exists('code', $params)) {
            throw new InvalidAuthorizationResponse(
                'Missing code from oauth/authorize.'
            );
        }

        $response = $request->postForm(
            'token',
            [
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
            ],
            [
                'Cookie' => 'npsso=' . Client::$npsso,
                'Authorization' => 'Basic MDk1MTUxNTktNzIzNy00MzcwLTliNDAtMzgwNmU2N2MwODkxOnVjUGprYTV0bnRCMktxc1A=',
            ]
        );

        $response = $response->json();

        static::$accessToken = $response['access_token'];
        static::$refreshToken = $response['refresh_token'];

        return static::$accessToken;
    }

    public static function getBaseUrl(): ?string
    {
        return 'https://ca.account.sony.com/api/authz/v3/oauth/';
    }

    public static function getInstanceUrl(string $id): ?string
    {
        return null;
    }
}
