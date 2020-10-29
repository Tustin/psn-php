<?php

namespace PlayStation;

use PlayStation\Api\Game;
use PlayStation\Api\User;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;

use PlayStation\Api\Community;
use PlayStation\Http\HttpClient;
use PlayStation\Api\MessageThread;

use PlayStation\Http\ResponseParser;
use PlayStation\Http\TokenMiddleware;

class Client {

    const AUTH_API = 'https://ca.account.sony.com/api/';
    const BASE_URL = 'https://m.np.playstation.net/api/';

    private const CLIENT_ID = '8c52bc6a-4ad1-43fb-bd63-4465cf818937';
    private const CLIENT_SECRET = 'bKC6jEYJ6CCXdxzr';
    private const DUID = '0000000d00040080027BC1C3FBB84112BFC9A4300A78E96A';
    private const SCOPE = 'openid:age openid:content_ctrl kamaji:get_privacy_settings kamaji:get_account_hash openid:user_id openid:ctry_code openid:lang';

    private $httpClient;
    private $onlineId;
    private $messageThreads;
    private $options;

    private $accessToken;
    private $refreshToken;
    private $expiresIn;

    public function __construct(array $options = [])
    {
        $this->options = array_merge([
            'base_uri' => self::AUTH_API
        ], $options);
        $this->httpClient = new HttpClient(new \GuzzleHttp\Client($this->options));
    }
    
    /**
     * Login to PlayStation network using a refresh token or 2FA.
     *
     * @param string $ticketUuidOrRefreshToken Ticket UUID for 2FA, or the refresh token.
     * @param string $code 2FA code sent to your device (ignore if using refresh token).
     * @return void
     */
    public function login(string $ticketUuidOrRefreshToken, string $code = null) 
    {
        // @TEMP
        throw new \Exception("broken");
        if ($code === null) {
            $response = $this->httpClient()->post(self::AUTH_API . 'oauth/token', [
                "app_context" => "inapp_ios",
                "client_id" => self::CLIENT_ID,
                "client_secret" => self::CLIENT_SECRET,
                "refresh_token" => $ticketUuidOrRefreshToken,
                "duid" => self::DUID,
                "grant_type" => "refresh_token",
                "scope" => self::SCOPE
            ]);
        } else {
            $response = $this->httpClient()->post(self::AUTH_API . 'ssocookie', [
                'authentication_type' => 'two_step',
                'ticket_uuid' => $ticketUuidOrRefreshToken,
                'code' => $code,
                'client_id' => self::CLIENT_ID
            ]);

            $npsso = $response->npsso;

            $response = $this->httpClient()->get(self::AUTH_API . 'oauth/authorize', [
                'duid' => self::DUID,
                'client_id' => self::CLIENT_ID,
                'response_type' => 'code',
                'scope' => self::SCOPE,
                'redirect_uri' => 'com.playstation.PlayStationApp://redirect'
            ], [
                'Cookie' => 'npsso=' . $npsso
            ]);

            if (($response instanceof Response) === false) {
                throw new \Exception('Unexpected response');
            }

            $grant = $response->getHeaderLine('X-NP-GRANT-CODE');

            if (empty($grant)) {
                throw new \Exception('Unable to get X-NP-GRANT-CODE');
            }

            $response = $this->httpClient()->post(self::AUTH_API . 'oauth/token', [
                'client_id' => self::CLIENT_ID,
                'client_secret' => self::CLIENT_SECRET,
                'duid' => self::DUID,
                'scope' => self::SCOPE,
                'redirect_uri' => 'com.playstation.PlayStationApp://redirect',
                'code' => $grant,
                'grant_type' => 'authorization_code'
            ]);

        }

        $this->accessToken = $response->access_token;
        $this->refreshToken = $response->refresh_token;
        $this->expiresIn = $response->expires_in;

        $this->httpClient = $this->createTokenMiddleware($this->accessToken);
    }

    /**
     * Creates a new HttpClient using middleware that adds the access token to the header of each request.
     *
     * @param string $accessToken PlayStation access token.
     * @return HttpClient
     */
    private function createTokenMiddleware(string $accessToken) : HttpClient
    {
        $handler = \GuzzleHttp\HandlerStack::create();

        $handler->push(
            Middleware::mapRequest(
                new TokenMiddleware($accessToken)
            )
        );

        $newOptions = array_merge([
            'handler' => $handler,
            'base_uri' => self::BASE_URL
        ], $this->options);
    
        return new HttpClient(new \GuzzleHttp\Client($newOptions));
    }


    public function loginWithNpsso(string $npsso)
    {
        // With the PS App revamp, we now need a JWT token.
        $response = $this->httpClient()->get('authz/v3/oauth/authorize', [
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
        
        if ($response->getStatusCode() !== 302) {
            // TODO: Throw proper exception here.
            throw new \Exception('Invalid response code from oauth/autorize.');
        }

        $location = $response->getHeader('Location');

        if (!$location) {
            throw new \Exception('Missing redirect location from oauth/authorize.');
        }

        parse_str(parse_url($location[0], PHP_URL_QUERY), $params);

        if (!array_key_exists('code', $params)) {
            throw new \Exception('Missing code from oauth/authorize.');
        }

        $response = $this->httpClient()->post('authz/v3/oauth/token', [
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
        ], HttpClient::FORM, [
            'Cookie' => 'npsso=' . $npsso,
            'Authorization' => 'Basic YWM4ZDE2MWEtZDk2Ni00NzI4LWIwZWEtZmZlYzIyZjY5ZWRjOkRFaXhFcVhYQ2RYZHdqMHY=',
        ]);

        $this->accessToken = $response->access_token;
        $this->refreshToken = $response->refresh_token;
        $this->expiresIn = $response->expires_in;

        $this->httpClient = $this->createTokenMiddleware($this->accessToken);
    }

    /**
     * Access the PlayStation API using an existing access token.
     *
     * @param string $accessToken
     * @return void
     */
    public function setAccessToken(string $accessToken)
    {
        $this->accessToken = $accessToken;

        $this->httpClient = $this->createTokenMiddleware($this->accessToken);
    }

    /**
     * Gets the HttpClient.
     *
     * @return HttpClient
     */
    public function httpClient() : HttpClient
    {
        return $this->httpClient;
    }

    /**
     * Gets the logged in user's onlineId.
     *
     * @return string
     */
    public function onlineId() : string
    {
        if ($this->onlineId === null) {
            $response = $this->httpClient()->get(sprintf(User::USERS_ENDPOINT . 'profile2', 'me'), [
                'fields' => 'onlineId'
            ]);

            $this->onlineId = $response->profile->onlineId;
        }
        return $this->onlineId;
    }

    /**
     * Gets the access token.
     *
     * @return string
     */
    public function accessToken() : string
    {
        return $this->accessToken;
    }

    /**
     * Gets the refresh token.
     *
     * @return string
     */
    public function refreshToken() : string
    {
        return $this->refreshToken;
    }

    /**
     * Gets the access token expire DateTime.
     *
     * @return \DateTime
     */
    public function expireDate() : \DateTime
    {
        return new \DateTime(sprintf('+%d seconds', $this->expiresIn));
    }

    /**
     * Gets all MessageThreads for the current Client.
     *
     * @param integer $offset Where to start.
     * @param integer $limit Amount of threads.
     * @return object
     */
    public function messageThreads(int $offset = 0, int $limit = 20) : \stdClass
    {
        if ($this->messageThreads === null) {
            $response = $this->httpClient()->get(MessageThread::MESSAGE_THREAD_ENDPOINT . 'threads/', [
                'fields' => 'threadMembers',
                'limit' => $limit,
                'offset' => $offset,
                'sinceReceivedDate' => '1970-01-01T00:00:00Z' // Don't hardcode
            ]);

            $this->messageThreads = $response;
        }
        return $this->messageThreads;
    }

    /**
     * Creates a new User object.
     *
     * @param string $onlineId The User's onlineId (null to get current User's account).
     * @return PlayStation\Api\User
     */
    public function user(string $onlineId = '') : User
    {
        return new User($this, $onlineId);
    }

    public function aa()
    {
        $this->httpClient()->get('https://m.np.playstation.net/api/userProfile/v1/internal/users/profiles?accountIds=290499036638149769%2C6302737558727256776%2C5767969705352314860%2C8660297613407809012%2C3318601581187974976%2C1794949560635596299%2C4468193837129027570%2C937218632316989458%2C5695843137456681910%2C7230702695231133866%2C2673037779524917826%2C7794101426778942609%2C3863143976691624727%2C3387758873256323321%2C7200968299130045846%2C643742757001727694%2C7927222913481535097%2C7886051232230562055%2C1220975848120722090%2C3169237629135083655%2C2813495812580870637%2C7919012979851873890%2C7068032807127798573%2C4959981001468776598%2C2805295645697401011%2C2964358804368618665%2C4645077389662723469%2C5794783855171231964%2C7900415681776679368%2C2486997276581965084%2C2493426535140808664%2C9080896438515493000%2C7130601423182633922%2C3912091914131564086%2C4320405660322087082%2C2196492594985375697%2C4355907905257225891%2C4659648722389483350%2C7839997113793959515%2C1021019581734755711%2C8914630277991330655%2C714356860632959838%2C771197688321275726%2C5439800386292523989%2C461813134570437919%2C8757422912664509555%2C6761352691515275753%2C6374239285526955388%2C585828778556322229%2C2379992714711872512%2C4367308563909230524%2C53223675607332110%2C1343596725589745841%2C1075970181868891095%2C1589844052109037392%2C2038382839787618096%2C6190288666262892979%2C8838352505110018759%2C7821507023663338445%2C7798383085517380875%2C548843795526398268%2C3404056621793690129%2C2211100396049156590%2C2029064944159430032%2C6804785205228014029%2C9036049092293166783%2C7959627026491411880%2C5606864473888784809%2C1865326376723044044%2C6236990150345940032%2C1751272709830908318%2C7124328791336006328%2C2979333742140583485%2C7872826187695676502%2C3808346567713520103%2C4598840736712551038%2C617023336643399668%2C4289768539348637785%2C6037662901497980231%2C8123693936931797193%2C6182351992776041204%2C6991911350856527680%2C573016432886960867%2C368118438435212941%2C5768080933006748975%2C8303463348201408600%2C4100202504455308276%2C7878355682701390793%2C6555173597669780588%2C2366605971145003267%2C4485389378206716984%2C7995893745498737045%2C7318283285218908995%2C7092186148169881433%2C13637444542984362%2C3948047011464002841%2C3142186831889868855%2C8186815934450134368%2C4923378550674279772%2C6342687153230660965');
    }

    /**
     * Find a game by it's title ID and return a new Game object.
     *
     * @param string $titleId The Game's title ID
     * @return PlayStation\Api\Game
     */
    public function game(string $titleId) : Game
    {
        return new Game($this, $titleId);
    }

    /**
     * Get or create a Community.
     *
     * @param string $communityIdOrName Community ID or the name of the new community.
     * @param string $type 
     * @param string $titleId Game to associate your Community with.
     * @return PlayStation\Api\Community
     */
    public function community(string $communityIdOrName, string $type = '', string $titleId = '') : Community
    {
        if (!empty($type) && !empty($titleId)) {
            // Create the Community.
            $response = $this->httpClient()->post(Community::COMMUNITY_ENDPOINT . 'communities?action=create', [
                'name' => $communityIdOrName,
                'type' => $type,
                'titleId' => $titleId
            ], HttpClient::JSON);

            $communityIdOrName = $response->id;
        }

        return new Community($this, $communityIdOrName);
    }
}