<?php

namespace OAuthTest\Unit\OAuth1\Service;

use OAuth\OAuth1\Service\Yahoo;

class YahooTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers OAuth\OAuth1\Service\Yahoo::__construct
     */
    public function testConstructCorrectInterfaceWithoutCustomUri()
    {
        $service = new Yahoo(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Service\\ServiceInterface', $service);
    }

    /**
     * @covers OAuth\OAuth1\Service\Yahoo::__construct
     */
    public function testConstructCorrectInstanceWithoutCustomUri()
    {
        $service = new Yahoo(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth1\Service\Yahoo::__construct
     */
    public function testConstructCorrectInstanceWithCustomUri()
    {
        $service = new Yahoo(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            'some-url'
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth1\Service\Yahoo::__construct
     * @covers OAuth\OAuth1\Service\Yahoo::getRequestTokenEndpoint
     */
    public function testGetRequestTokenEndpoint()
    {
        $service = new Yahoo(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertSame(
            'https://api.login.yahoo.com/oauth/v2/get_request_token',
            (string) $service->getRequestTokenEndpoint()
        );
    }

    /**
     * @covers OAuth\OAuth1\Service\Yahoo::__construct
     * @covers OAuth\OAuth1\Service\Yahoo::getAuthorizationEndpoint
     */
    public function testGetAuthorizationEndpoint()
    {
        $service = new Yahoo(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertSame(
            'https://api.login.yahoo.com/oauth/v2/request_auth',
            (string) $service->getAuthorizationEndpoint()
        );
    }

    /**
     * @covers OAuth\OAuth1\Service\Yahoo::__construct
     * @covers OAuth\OAuth1\Service\Yahoo::getAccessTokenEndpoint
     */
    public function testGetAccessTokenEndpoint()
    {
        $service = new Yahoo(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertSame(
            'https://api.login.yahoo.com/oauth/v2/get_token',
            (string) $service->getAccessTokenEndpoint()
        );
    }

    /**
     * @covers OAuth\OAuth1\Service\Yahoo::__construct
     * @covers OAuth\OAuth1\Service\Yahoo::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Yahoo::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnNulledResponse()
    {
        /** @var Yahoo|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth1\\Service\\Yahoo',
            ['httpRequest'],
            [
                $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->getMock('\\Buzz\\Browser'),
                $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
                $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
            ]
        );

        $service->expects($this->once())->method('httpRequest')->will($this->returnValue(null));

        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestRequestToken();
    }

    /**
     * @covers OAuth\OAuth1\Service\Yahoo::__construct
     * @covers OAuth\OAuth1\Service\Yahoo::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Yahoo::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnResponseNotAnArray()
    {
        /** @var Yahoo|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth1\\Service\\Yahoo',
            ['httpRequest'],
            [
                $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->getMock('\\Buzz\\Browser'),
                $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
                $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
            ]
        );

        $service->expects($this->once())->method('httpRequest')->will($this->returnValue('notanarray'));

        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestRequestToken();
    }

    /**
     * @covers OAuth\OAuth1\Service\Yahoo::__construct
     * @covers OAuth\OAuth1\Service\Yahoo::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Yahoo::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnResponseCallbackNotSet()
    {
        /** @var Yahoo|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth1\\Service\\Yahoo',
            ['httpRequest'],
            [
                $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->getMock('\\Buzz\\Browser'),
                $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
                $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
            ]
        );

        $service->expects($this->once())->method('httpRequest')->will($this->returnValue('foo=bar'));

        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestRequestToken();
    }

    /**
     * @covers OAuth\OAuth1\Service\Yahoo::__construct
     * @covers OAuth\OAuth1\Service\Yahoo::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Yahoo::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnResponseCallbackNotTrue()
    {
        /** @var Yahoo|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth1\\Service\\Yahoo',
            ['httpRequest'],
            [
                $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->getMock('\\Buzz\\Browser'),
                $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
                $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
            ]
        );

        $service->expects($this->once())->method('httpRequest')->will(
            $this->returnValue(
                'oauth_callback_confirmed=false'
            )
        );

        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestRequestToken();
    }

    /**
     * @covers OAuth\OAuth1\Service\Yahoo::__construct
     * @covers OAuth\OAuth1\Service\Yahoo::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Yahoo::parseRequestTokenResponse
     * @covers OAuth\OAuth1\Service\Yahoo::parseAccessTokenResponse
     */
    public function testParseRequestTokenResponseValid()
    {
        /** @var Yahoo|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth1\\Service\\Yahoo',
            ['httpRequest'],
            [
                $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->getMock('\\Buzz\\Browser'),
                $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
                $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
            ]
        );

        $service->expects($this->once())->method('httpRequest')->will(
            $this->returnValue(
                'oauth_callback_confirmed=true&oauth_token=foo&oauth_token_secret=bar'
            )
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Token\\StdOAuth1Token', $service->requestRequestToken());
    }

    /**
     * @covers OAuth\OAuth1\Service\Yahoo::__construct
     * @covers OAuth\OAuth1\Service\Yahoo::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Yahoo::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnError()
    {
        $token = $this->getMock('\\OAuth\\OAuth1\\Token\\TokenInterface');

        $storage = $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

        /** @var Yahoo|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth1\\Service\\Yahoo',
            ['httpRequest'],
            [
                $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->getMock('\\Buzz\\Browser'),
                $storage,
                $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
            ]
        );

        $service->expects($this->once())->method('httpRequest')->will($this->returnValue('error=bar'));

        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo', 'bar', $token);
    }

    /**
     * @covers OAuth\OAuth1\Service\Yahoo::__construct
     * @covers OAuth\OAuth1\Service\Yahoo::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Yahoo::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseValid()
    {
        $token = $this->getMock('\\OAuth\\OAuth1\\Token\\TokenInterface');

        $storage = $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

        /** @var Yahoo|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth1\\Service\\Yahoo',
            ['httpRequest'],
            [
                $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->getMock('\\Buzz\\Browser'),
                $storage,
                $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
            ]
        );

        $service->expects($this->once())->method('httpRequest')->will(
            $this->returnValue(
                'oauth_token=foo&oauth_token_secret=bar'
            )
        );

        $this->assertInstanceOf(
            '\\OAuth\\OAuth1\\Token\\StdOAuth1Token',
            $service->requestAccessToken('foo', 'bar', $token)
        );
    }

    /**
     * @covers OAuth\OAuth1\Service\Yahoo::request
     */
    public function testRequest()
    {
        $token = $this->getMock('\\OAuth\\OAuth1\\Token\\TokenInterface');

        $storage = $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

        /** @var Yahoo|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth1\\Service\\Yahoo',
            ['httpRequest'],
            [
                $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->getMock('\\Buzz\\Browser'),
                $storage,
                $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
            ]
        );

        $service->expects($this->once())->method('httpRequest')->will($this->returnValue('response!'));

        $this->assertSame('response!', $service->request('/my/awesome/path'));
    }
}
