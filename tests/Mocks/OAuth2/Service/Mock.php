<?php

namespace OAuthTest\Mocks\OAuth2\Service;

use OAuth\OAuth2\Service\AbstractService;
use OAuth\OAuth2\Token\StdOAuth2Token;

class Mock extends AbstractService
{

    const SCOPE_MOCK = 'mock';
    const SCOPE_MOCK_2 = 'mock2';

    protected $authorizationEndpoint = 'http://pieterhordijk.com/auth/{apiVersion}';
    protected $accessTokenEndpoint = 'http://pieterhordijk.com/access/{apiVersion}';
    protected $authorizationMethod = null;

    protected function parseAccessTokenResponse($responseBody)
    {
        return new StdOAuth2Token();
    }

    // this allows us to set different auth methods for tests
    public function setAuthorizationMethod($method)
    {
        $this->authorizationMethod = $method;
    }

    public function httpRequest($uri, array $body = [], array $headers = [], $method = 'POST')
    {
        return [
            'uri'     => $uri,
            'body'    => $body,
            'headers' => $headers,
            'method'  => $method
        ];
    }
}