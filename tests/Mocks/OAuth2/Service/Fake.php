<?php

namespace OAuthTest\Mocks\OAuth2\Service;

use Buzz\Browser;
use OAuth\Common\Consumer\CredentialsInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\OAuth2\Service\AbstractService;

class Fake extends AbstractService
{
    const SCOPE_FOO    = 'https://www.pieterhordijk.com/auth';
    const SCOPE_CUSTOM = 'custom';

	public function __construct(
		CredentialsInterface $credentials,
		Browser $httpTransporter,
		TokenStorageInterface $storage,
		array $scopes = [],
		$baseApiUri = NULL,
		$stateParameterInAutUrl = FALSE,
		$apiVersion = ""
	)
	{
	}


	/**
     * {@inheritdoc}
     */
    protected function parseAccessTokenResponse($responseBody)
    {
    }
}