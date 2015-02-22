<?php

namespace OAuthTest\Unit\OAuth1\Service;

use OAuth\OAuth1\Service\Etsy;

class EtsyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers OAuth\OAuth1\Service\Etsy::__construct
     */
    public function testConstructCorrectInterfaceWithoutCustomUri()
    {
        $service = new Etsy(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Service\\ServiceInterface', $service);
    }

    /**
     * @covers OAuth\OAuth1\Service\Etsy::__construct
     */
    public function testConstructCorrectInstanceWithoutCustomUri()
    {
        $service = new Etsy(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth1\Service\Etsy::__construct
     */
    public function testConstructCorrectInstanceWithCustomUri()
    {
        $service = new Etsy(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            'some-url'
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth1\Service\Etsy::__construct
     * @covers OAuth\OAuth1\Service\Etsy::getRequestTokenEndpoint
     */
    public function testGetRequestTokenEndpoint()
    {
        $service = new Etsy(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertSame(
            'https://openapi.etsy.com/v2/oauth/request_token',
            (string) $service->getRequestTokenEndpoint()
        );

		$service->setScopes(array('email_r', 'cart_rw'));

        $this->assertSame(
            'https://openapi.etsy.com/v2/oauth/request_token?scope=email_r%20cart_rw',
            (string) $service->getRequestTokenEndpoint()
        );

    }

    /**
     * @covers OAuth\OAuth1\Service\Etsy::__construct
     * @covers OAuth\OAuth1\Service\Etsy::getAccessTokenEndpoint
     */
    public function testGetAccessTokenEndpoint()
    {
        $service = new Etsy(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertSame(
            'https://openapi.etsy.com/v2/oauth/access_token',
            (string) $service->getAccessTokenEndpoint()
        );
    }

    /**
     * @covers OAuth\OAuth1\Service\Etsy::__construct
     * @covers OAuth\OAuth1\Service\Etsy::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Etsy::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnNulledResponse()
    {
        /** @var Etsy|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth1\\Service\\Etsy', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
			$this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnValue(null));

        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestRequestToken();
    }

    /**
     * @covers OAuth\OAuth1\Service\Etsy::__construct
     * @covers OAuth\OAuth1\Service\Etsy::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Etsy::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnResponseNotAnArray()
    {
        /** @var Etsy|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth1\\Service\\Etsy', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
			$this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnValue('notanarray'));

        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestRequestToken();
    }

    /**
     * @covers OAuth\OAuth1\Service\Etsy::__construct
     * @covers OAuth\OAuth1\Service\Etsy::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Etsy::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnResponseCallbackNotSet()
    {
        /** @var Etsy|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth1\\Service\\Etsy', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
			$this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnValue('foo=bar'));

        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestRequestToken();
    }

    /**
     * @covers OAuth\OAuth1\Service\Etsy::__construct
     * @covers OAuth\OAuth1\Service\Etsy::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Etsy::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnResponseCallbackNotTrue()
    {
	    /** @var Etsy|\PHPUnit_Framework_MockObject_MockObject $service */
	    $service = $this->getMock('\\OAuth\\OAuth1\\Service\\Etsy', ['httpRequest'], [
		    $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
		    $this->getMock('\\Buzz\\Browser'),
		    $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
		    $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
	    ]);

	    $service->expects($this->once())->method('httpRequest')->will($this->returnValue(
		    'oauth_callback_confirmed=false'
	    ));

        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestRequestToken();
    }

    /**
     * @covers OAuth\OAuth1\Service\Etsy::__construct
     * @covers OAuth\OAuth1\Service\Etsy::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Etsy::parseRequestTokenResponse
     * @covers OAuth\OAuth1\Service\Etsy::parseAccessTokenResponse
     */
    public function testParseRequestTokenResponseValid()
    {
        /** @var Etsy|\PHPUnit_Framework_MockObject_MockObject $service */
	    $service = $this->getMock('\\OAuth\\OAuth1\\Service\\Etsy', ['httpRequest'], [
		    $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
		    $this->getMock('\\Buzz\\Browser'),
		    $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
		    $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
	    ]);

	    $service->expects($this->once())->method('httpRequest')->will($this->returnValue(
		    'oauth_callback_confirmed=true&oauth_token=foo&oauth_token_secret=bar'
	    ));

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Token\\StdOAuth1Token', $service->requestRequestToken());
    }

    /**
     * @covers OAuth\OAuth1\Service\Etsy::__construct
     * @covers OAuth\OAuth1\Service\Etsy::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Etsy::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnError()
    {
        $token = $this->getMock('\\OAuth\\OAuth1\\Token\\TokenInterface');
        $storage = $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

		/** @var Etsy|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth1\\Service\\Etsy', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$storage,
			$this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnValue('error=bar'));

        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo', 'bar', $token);
    }

    /**
     * @covers OAuth\OAuth1\Service\Etsy::__construct
     * @covers OAuth\OAuth1\Service\Etsy::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\Etsy::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseValid()
    {
	    $token = $this->getMock('\\OAuth\\OAuth1\\Token\\TokenInterface');
	    $storage = $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
	    $storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

	    /** @var Etsy|\PHPUnit_Framework_MockObject_MockObject $service */
	    $service = $this->getMock('\\OAuth\\OAuth1\\Service\\Etsy', ['httpRequest'], [
		    $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
		    $this->getMock('\\Buzz\\Browser'),
		    $storage,
		    $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
	    ]);

	    $service->expects($this->once())->method('httpRequest')->will($this->returnValue(
		    'oauth_token=foo&oauth_token_secret=bar'
	    ));

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Token\\StdOAuth1Token', $service->requestAccessToken('foo', 'bar', $token));
    }
}
