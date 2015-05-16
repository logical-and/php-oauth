<?php

namespace OAuthTest\Unit\OAuth2\Service;

use OAuth\Common\Token\TokenInterface;
use OAuth\OAuth2\Service\Instagram;

class InstagramTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers OAuth\OAuth2\Service\Instagram::__construct
     */
    public function testConstructCorrectInterfaceWithoutCustomUri()
    {
        $service = new Instagram(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\ServiceInterface', $service);
    }

    /**
     * @covers OAuth\OAuth2\Service\Instagram::__construct
     */
    public function testConstructCorrectInstanceWithoutCustomUri()
    {
        $service = new Instagram(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth2\Service\Instagram::__construct
     */
    public function testConstructCorrectInstanceWithCustomUri()
    {
        $service = new Instagram(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            [],
            'some-url'
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth2\Service\Instagram::__construct
     * @covers OAuth\OAuth2\Service\Instagram::getAuthorizationEndpoint
     */
    public function testGetAuthorizationEndpoint()
    {
        $service = new Instagram(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertSame(
            'https://api.instagram.com/oauth/authorize/',
            (string) $service->getAuthorizationEndpoint()
        );
    }

    /**
     * @covers OAuth\OAuth2\Service\Instagram::__construct
     * @covers OAuth\OAuth2\Service\Instagram::getAccessTokenEndpoint
     */
    public function testGetAccessTokenEndpoint()
    {
        $service = new Instagram(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertSame(
            'https://api.instagram.com/oauth/access_token',
            (string) $service->getAccessTokenEndpoint()
        );
    }

    /**
     * @covers OAuth\OAuth2\Service\Instagram::__construct
     * @covers OAuth\OAuth2\Service\Instagram::getAuthorizationMethod
     */
    public function testGetAuthorizationMethod()
    {
        $token = $this->getMock('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', ['getEndOfLife', 'getAccessToken']);
        $token->expects($this->once())->method('getEndOfLife')->will(
            $this->returnValue(TokenInterface::EOL_NEVER_EXPIRES)
        );
        $token->expects($this->once())->method('getAccessToken')->will($this->returnValue('foo'));

        $storage = $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

        /** @var Instagram|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth2\\Service\\Instagram',
            ['httpRequest'],
            [
                $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->getMock('\\Buzz\\Browser'),
                $storage
            ]
        );

        $service->expects($this->once())->method('httpRequest')->will($this->returnArgument(0));

        $uri = $service->request('https://pieterhordijk.com/my/awesome/path');
        $absoluteUri = parse_url((string) $uri);

        $this->assertSame('access_token=foo', $absoluteUri[ 'query' ]);
    }

    /**
     * @covers OAuth\OAuth2\Service\Instagram::__construct
     * @covers OAuth\OAuth2\Service\Instagram::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnNulledResponse()
    {
        /** @var Instagram|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth2\\Service\\Instagram',
            ['httpRequest'],
            [
                $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->getMock('\\Buzz\\Browser'),
                $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
            ]
        );

        $service->expects($this->once())->method('httpRequest')->will($this->returnValue(null));

        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo');
    }

    /**
     * @covers OAuth\OAuth2\Service\Instagram::__construct
     * @covers OAuth\OAuth2\Service\Instagram::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnError()
    {
        /** @var Instagram|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth2\\Service\\Instagram',
            ['httpRequest'],
            [
                $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->getMock('\\Buzz\\Browser'),
                $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
            ]
        );

        $service->expects($this->once())->method('httpRequest')->will($this->returnValue('error=some_error'));

        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo');
    }

    /**
     * @covers OAuth\OAuth2\Service\Instagram::__construct
     * @covers OAuth\OAuth2\Service\Instagram::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseValidWithoutRefreshToken()
    {
        /** @var Instagram|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth2\\Service\\Instagram',
            ['httpRequest'],
            [
                $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->getMock('\\Buzz\\Browser'),
                $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
            ]
        );

        $service->expects($this->once())->method('httpRequest')->will(
            $this->returnValue('{"access_token":"foo","expires_in":"bar"}')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
    }

    /**
     * @covers OAuth\OAuth2\Service\Instagram::__construct
     * @covers OAuth\OAuth2\Service\Instagram::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseValidWithRefreshToken()
    {
        /** @var Instagram|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth2\\Service\\Instagram',
            ['httpRequest'],
            [
                $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->getMock('\\Buzz\\Browser'),
                $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
            ]
        );

        $service->expects($this->once())->method('httpRequest')->will(
            $this->returnValue('{"access_token":"foo","expires_in":"bar","refresh_token":"baz"}')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
    }
}
