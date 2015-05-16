<?php

namespace OAuthTest\Unit\OAuth1\Service;

use OAuth\OAuth1\Service\FitBit;

class FitBitTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers OAuth\OAuth1\Service\FitBit::__construct
     */
    public function testConstructCorrectInterfaceWithoutCustomUri()
    {
        $service = new FitBit(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Service\\ServiceInterface', $service);
    }

    /**
     * @covers OAuth\OAuth1\Service\FitBit::__construct
     */
    public function testConstructCorrectInstanceWithoutCustomUri()
    {
        $service = new FitBit(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth1\Service\FitBit::__construct
     */
    public function testConstructCorrectInstanceWithCustomUri()
    {
        $service = new FitBit(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            'some-url'
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth1\Service\FitBit::__construct
     * @covers OAuth\OAuth1\Service\FitBit::getRequestTokenEndpoint
     */
    public function testGetRequestTokenEndpoint()
    {
        $service = new FitBit(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertSame(
            'https://api.fitbit.com/oauth/request_token',
            (string) $service->getRequestTokenEndpoint()
        );
    }

    /**
     * @covers OAuth\OAuth1\Service\FitBit::__construct
     * @covers OAuth\OAuth1\Service\FitBit::getAuthorizationEndpoint
     */
    public function testGetAuthorizationEndpoint()
    {
        $service = new FitBit(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertSame(
            'https://www.fitbit.com/oauth/authorize',
            (string) $service->getAuthorizationEndpoint()
        );
    }

    /**
     * @covers OAuth\OAuth1\Service\FitBit::__construct
     * @covers OAuth\OAuth1\Service\FitBit::getAccessTokenEndpoint
     */
    public function testGetAccessTokenEndpoint()
    {
        $service = new FitBit(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertSame(
            'https://api.fitbit.com/oauth/access_token',
            (string) $service->getAccessTokenEndpoint()
        );
    }

    /**
     * @covers OAuth\OAuth1\Service\FitBit::__construct
     * @covers OAuth\OAuth1\Service\FitBit::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\FitBit::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnNulledResponse()
    {
        /** @var FitBit|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth1\\Service\\FitBit',
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
     * @covers OAuth\OAuth1\Service\FitBit::__construct
     * @covers OAuth\OAuth1\Service\FitBit::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\FitBit::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnResponseNotAnArray()
    {
        /** @var FitBit|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth1\\Service\\FitBit',
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
     * @covers OAuth\OAuth1\Service\FitBit::__construct
     * @covers OAuth\OAuth1\Service\FitBit::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\FitBit::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnResponseCallbackNotSet()
    {
        /** @var FitBit|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth1\\Service\\FitBit',
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
     * @covers OAuth\OAuth1\Service\FitBit::__construct
     * @covers OAuth\OAuth1\Service\FitBit::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\FitBit::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnResponseCallbackNotTrue()
    {
        /** @var FitBit|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth1\\Service\\FitBit',
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
     * @covers OAuth\OAuth1\Service\FitBit::__construct
     * @covers OAuth\OAuth1\Service\FitBit::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\FitBit::parseRequestTokenResponse
     * @covers OAuth\OAuth1\Service\FitBit::parseAccessTokenResponse
     */
    public function testParseRequestTokenResponseValid()
    {
        /** @var FitBit|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth1\\Service\\FitBit',
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
     * @covers OAuth\OAuth1\Service\FitBit::__construct
     * @covers OAuth\OAuth1\Service\FitBit::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\FitBit::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnError()
    {
        $token = $this->getMock('\\OAuth\\OAuth1\\Token\\TokenInterface');
        $storage = $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

        /** @var FitBit|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth1\\Service\\FitBit',
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
     * @covers OAuth\OAuth1\Service\FitBit::__construct
     * @covers OAuth\OAuth1\Service\FitBit::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\FitBit::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseValid()
    {
        $token = $this->getMock('\\OAuth\\OAuth1\\Token\\TokenInterface');
        $storage = $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

        /** @var FitBit|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth1\\Service\\FitBit',
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
}
