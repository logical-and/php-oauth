<?php

namespace OAuthTest\Unit\OAuth2\Service;

use OAuth\Common\Token\TokenInterface;
use OAuth\OAuth2\Service\Foursquare;

class FoursquareTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers OAuth\OAuth2\Service\Foursquare::__construct
     */
    public function testConstructCorrectInterfaceWithoutCustomUri()
    {
        $service = new Foursquare(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\ServiceInterface', $service);
    }

    /**
     * @covers OAuth\OAuth2\Service\Foursquare::__construct
     */
    public function testConstructCorrectInstanceWithoutCustomUri()
    {
        $service = new Foursquare(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth2\Service\Foursquare::__construct
     */
    public function testConstructCorrectInstanceWithCustomUri()
    {
        $service = new Foursquare(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            [],
            'some-url'
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth2\Service\Foursquare::__construct
     * @covers OAuth\OAuth2\Service\Foursquare::getAuthorizationEndpoint
     */
    public function testGetAuthorizationEndpoint()
    {
        $service = new Foursquare(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertSame('https://foursquare.com/oauth2/authenticate', (string) $service->getAuthorizationEndpoint());
    }

    /**
     * @covers OAuth\OAuth2\Service\Foursquare::__construct
     * @covers OAuth\OAuth2\Service\Foursquare::getAccessTokenEndpoint
     */
    public function testGetAccessTokenEndpoint()
    {
        $service = new Foursquare(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertSame('https://foursquare.com/oauth2/access_token', (string) $service->getAccessTokenEndpoint());
    }

    /**
     * @covers OAuth\OAuth2\Service\Foursquare::__construct
     * @covers OAuth\OAuth2\Service\Foursquare::getAuthorizationMethod
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

        /** @var Foursquare|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth2\\Service\\Foursquare',
            ['httpRequest'],
            [
                $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->getMock('\\Buzz\\Browser'),
                $storage
            ]
        );

        $service->expects($this->once())->method('httpRequest')->will($this->returnArgument(2));

        $headers = $service->request('https://pieterhordijk.com/my/awesome/path');

        $this->assertTrue(array_key_exists('Authorization', $headers));
        $this->assertTrue(in_array('OAuth foo', $headers, true));
    }

    /**
     * @covers OAuth\OAuth2\Service\Foursquare::__construct
     * @covers OAuth\OAuth2\Service\Foursquare::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnNulledResponse()
    {
        /** @var Foursquare|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth2\\Service\\Foursquare',
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
     * @covers OAuth\OAuth2\Service\Foursquare::__construct
     * @covers OAuth\OAuth2\Service\Foursquare::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnError()
    {
        /** @var Foursquare|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth2\\Service\\Foursquare',
            ['httpRequest'],
            [
                $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->getMock('\\Buzz\\Browser'),
                $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
            ]
        );

        $service->expects($this->once())->method('httpRequest')->will($this->returnValue('{"error":"some_error"}'));

        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo');
    }

    /**
     * @covers OAuth\OAuth2\Service\Foursquare::__construct
     * @covers OAuth\OAuth2\Service\Foursquare::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseValidWithoutRefreshToken()
    {
        /** @var Foursquare|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth2\\Service\\Foursquare',
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
     * @covers OAuth\OAuth2\Service\Foursquare::__construct
     * @covers OAuth\OAuth2\Service\Foursquare::request
     */
    public function testRequest()
    {
        $token = $this->getMock('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', ['getEndOfLife', 'getAccessToken']);
        $token->expects($this->once())->method('getEndOfLife')->will(
            $this->returnValue(TokenInterface::EOL_NEVER_EXPIRES)
        );
        $token->expects($this->once())->method('getAccessToken')->will($this->returnValue('foo'));

        $storage = $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

        /** @var Foursquare|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth2\\Service\\Foursquare',
            ['httpRequest'],
            [
                $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->getMock('\\Buzz\\Browser'),
                $storage
            ]
        );

        $service->expects($this->once())->method('httpRequest')->will($this->returnArgument(0));

        $this->assertSame(
            'https://pieterhordijk.com/my/awesome/path?v=20130829',
            (string) $service->request('https://pieterhordijk.com/my/awesome/path')
        );
    }
}
