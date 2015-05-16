<?php

namespace OAuthTest\Mocks\OAuth1\Service;

use Buzz\Browser;
use OAuth\Common\Consumer\CredentialsInterface;
use OAuth\Common\Http\Url;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\OAuth1\Service\AbstractService;
use OAuth\OAuth1\Signature\SignatureInterface;

class Fake extends AbstractService
{

    public function __construct(
        CredentialsInterface $credentials,
        Browser $httpTransporter,
        TokenStorageInterface $storage,
        SignatureInterface $signature,
        Url $baseApiUri = null
    ) {
    }

    /**
     * {inheritDoc}
     */
    protected function parseRequestTokenResponse($responseBody)
    {
    }

    /**
     * {inheritDoc}
     */
    protected function parseAccessTokenResponse($responseBody)
    {
    }
}
