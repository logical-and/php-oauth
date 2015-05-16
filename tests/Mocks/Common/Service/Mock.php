<?php

namespace OAuthTest\Mocks\Common\Service;

use Buzz\Browser;
use OAuth\Common\Consumer\CredentialsInterface;
use OAuth\Common\Service\AbstractService;
use OAuth\Common\Storage\TokenStorageInterface;

class Mock extends AbstractService
{

    public function __construct(
        CredentialsInterface $credentials,
        Browser $httpTransporter,
        TokenStorageInterface $storage,
        $baseApiUrl
    ) {
        parent::__construct($credentials, $httpTransporter, $storage, $baseApiUrl);
    }

    /**
     * {@inheritdoc}
     */
    public function request($path, array $body = [], $method = 'GET', array $extraHeaders = [])
    {
    }

    /**
     * Returns the url to redirect to for authorization purposes.
     *
     * @param array $additionalParameters
     *
     * @return UriInterface
     */
    public function getAuthorizationUri(array $additionalParameters = [])
    {
    }

    /**
     * Returns the authorization API endpoint.
     *
     * @return UriInterface
     */
    public function getAuthorizationEndpoint()
    {
    }

    /**
     * Returns the access token API endpoint.
     *
     * @return UriInterface
     */
    public function getAccessTokenEndpoint()
    {
    }

    public function testDetermineRequestUriFromPath($path)
    {
        return $this->determineRequestUriFromPath($path);
    }
}
