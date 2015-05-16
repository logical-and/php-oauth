<?php

namespace OAuthTest\Unit\OAuth2\Service;

use OAuth\OAuth2\Service\Google;

class GoogleTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers OAuth\OAuth2\Service\Google::__construct
     */
    public function testConstructCorrectInterfaceWithoutCustomUri()
    {
        $service = new Google(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\ServiceInterface', $service);
    }

    /**
     * @covers OAuth\OAuth2\Service\Google::__construct
     */
    public function testConstructCorrectInstanceWithoutCustomUri()
    {
        $service = new Google(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth2\Service\Google::__construct
     */
    public function testConstructCorrectInstanceWithCustomUri()
    {
        $service = new Google(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            [],
            'some-url'
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth2\Service\Google::__construct
     * @covers OAuth\OAuth2\Service\Google::getAuthorizationEndpoint
     */
    public function testGetAuthorizationEndpoint()
    {
        $service = new Google(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertSame(
            'https://accounts.google.com/o/oauth2/auth?access_type=online',
            (string) $service->getAuthorizationEndpoint()
        );

        // Verify that 'offine' works
        $service->setAccessType('offline');
        $this->assertSame(
            'https://accounts.google.com/o/oauth2/auth?access_type=offline',
            (string) $service->getAuthorizationEndpoint()
        );
    }

    /**
     * @covers OAuth\OAuth2\Service\Google::__construct
     * @covers OAuth\OAuth2\Service\Google::getAuthorizationEndpoint
     */
    public function testGetAuthorizationEndpointException()
    {
        $service = new Google(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->setExpectedException('OAuth\OAuth2\Service\Exception\InvalidAccessTypeException');

        try {
            $service->setAccessType('invalid');
        } catch (InvalidAccessTypeException $e) {
            return;
        }
        $this->fail('Expected InvalidAccessTypeException not thrown');
    }

    /**
     * @covers OAuth\OAuth2\Service\Google::__construct
     * @covers OAuth\OAuth2\Service\Google::getAccessTokenEndpoint
     */
    public function testGetAccessTokenEndpoint()
    {
        $service = new Google(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertSame(
            'https://accounts.google.com/o/oauth2/token',
            (string) $service->getAccessTokenEndpoint()
        );
    }

    /**
     * @covers OAuth\OAuth2\Service\Google::__construct
     * @covers OAuth\OAuth2\Service\Google::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnNulledResponse()
    {
        /** @var Google|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth2\\Service\\Google',
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
     * @covers OAuth\OAuth2\Service\Google::__construct
     * @covers OAuth\OAuth2\Service\Google::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnError()
    {
        /** @var Google|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth2\\Service\\Google',
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
     * @covers OAuth\OAuth2\Service\Google::__construct
     * @covers OAuth\OAuth2\Service\Google::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseValidWithoutRefreshToken()
    {
        /** @var Google|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth2\\Service\\Google',
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
     * @covers OAuth\OAuth2\Service\Google::__construct
     * @covers OAuth\OAuth2\Service\Google::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseValidWithRefreshToken()
    {
        /** @var Google|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth2\\Service\\Google',
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
