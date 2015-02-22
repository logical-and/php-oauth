<?php

namespace OAuthTest\Mocks\OAuth1\Service;

use OAuth\OAuth1\Service\AbstractService;
use OAuth\Common\Http\Uri\Uri;
use OAuth\OAuth1\Token\StdOAuth1Token;

class Mock extends AbstractService
{
	protected $requestTokenEndpoint = 'http://pieterhordijk.com/token';
	protected $authorizationEndpoint = 'http://pieterhordijk.com/auth';
	protected $accessTokenEndpoint = 'http://pieterhordijk.com/access';

	public function httpRequest($uri, array $body = [], array $headers = [], $method = 'POST')
	{
		return [
			'uri' => $uri,
			'body' => $body,
			'headers' => $headers,
			'method' => $method
		];
	}

    protected function parseRequestTokenResponse($responseBody)
    {
	    $token = new StdOAuth1Token();
	    $token->setRequestToken($responseBody);

        return $token;
    }

    protected function parseAccessTokenResponse($responseBody)
    {
	    $token = new StdOAuth1Token();
	    $token->setAccessToken($responseBody);

	    return $token;
    }
}
