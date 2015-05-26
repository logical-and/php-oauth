<?php
namespace OAuth\OAuth2\Service;

use OAuth\Common\Exception\Exception;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\OAuth2\Token\StdOAuth2Token;

class BattleNet extends AbstractService
{

    /**
     * Scopes
     *
     * @var string
     */

    const SCOPE_WOW_PROFILE = 'wow.profile';
    const SCOPE_SC2_PROFILE = 'sc2.profile';
    const REGION_CN = 'cn';
    const REGION_EU = 'eu';
    const REGION_KR = 'kr';
    const REGION_TW = 'tw';
    const REGION_US = 'us';

    protected $baseApiUri = 'https://{region}.api.battle.net/';
    protected $authorizationEndpoint = 'https://{region}.battle.net/oauth/authorize';
    protected $accessTokenEndpoint = 'https://{region}.battle.net/oauth/token';
    protected $authorizationMethod = self::AUTHORIZATION_METHOD_QUERY_STRING;

    /**
     * BattleNet Region
     *
     * @var string
     */
    protected $region;

    /**
     * Set region based on constants
     * $service->setRegion(BattleNet::REGION_US)
     *
     * @param $region
     *
     * @return $this
     * @throws Exception
     */
    public function setRegion($region)
    {
        // Check region

        $reflClass = new \ReflectionClass($this);
        $constants = $reflClass->getConstants();
        $regions = [];

        foreach ($constants as $constant => $value) {
            if (0 === strpos($constant, 'REGION_')) {
                $regions[] = $value;
            }
        }
        if (!in_array($region, $regions)) {
            throw new Exception("Region \"$region\" is unknown!");
        }

        $this->region = $this->urlPlaceholders[ 'region' ] = $region;

        return $this;
    }

    /**
     * @param string $responseBody
     *
     * @return \OAuth\Common\Token\TokenInterface|\OAuth\OAuth2\Token\StdOAuth2Token
     * @throws \OAuth\Common\Http\Exception\TokenResponseException
     */
    protected function parseAccessTokenResponse($responseBody)
    {
        $data = json_decode($responseBody, true);

        if (null === $data || !is_array($data)) {
            throw new TokenResponseException('Unable to parse response.');
        } elseif (isset($data[ 'error' ])) {
            throw new TokenResponseException('Error in retrieving token: "' . $data[ 'error' ] . '"');
        }

        $token = new StdOAuth2Token();

        $token->setAccessToken($data[ 'access_token' ]);
        // I'm invincible!!!
        $token->setEndOfLife(StdOAuth2Token::EOL_NEVER_EXPIRES);
        unset($data[ 'access_token' ]);
        $token->setExtraParams($data);

        return $token;
    }
}