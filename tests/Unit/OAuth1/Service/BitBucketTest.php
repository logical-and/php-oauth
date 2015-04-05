<?php

namespace OAuthTest\Unit\OAuth1\Service;

use OAuth\OAuth1\Service\BitBucket;

class BitBucketTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers OAuth\OAuth1\Service\BitBucket::__construct
     */
    public function testConstructCorrectInterfaceWithoutCustomUri()
    {
        $service = new BitBucket(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Service\\ServiceInterface', $service);
    }

    /**
     * @covers OAuth\OAuth1\Service\BitBucket::__construct
     */
    public function testConstructCorrectInstanceWithoutCustomUri()
    {
        $service = new BitBucket(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth1\Service\BitBucket::__construct
     */
    public function testConstructCorrectInstanceWithCustomUri()
    {
        $service = new BitBucket(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
            'some-url'
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth1\Service\BitBucket::__construct
     * @covers OAuth\OAuth1\Service\BitBucket::getRequestTokenEndpoint
     */
    public function testGetRequestTokenEndpoint()
    {
        $service = new BitBucket(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertSame(
            'https://bitbucket.org/%21api/1.0/oauth/request_token',
            (string) $service->getRequestTokenEndpoint()
        );
    }

    /**
     * @covers OAuth\OAuth1\Service\BitBucket::__construct
     * @covers OAuth\OAuth1\Service\BitBucket::getAuthorizationEndpoint
     */
    public function testGetAuthorizationEndpoint()
    {
        $service = new BitBucket(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertSame(
            'https://bitbucket.org/%21api/1.0/oauth/authenticate',
            (string) $service->getAuthorizationEndpoint()
        );
    }

    /**
     * @covers OAuth\OAuth1\Service\BitBucket::__construct
     * @covers OAuth\OAuth1\Service\BitBucket::getAccessTokenEndpoint
     */
    public function testGetAccessTokenEndpoint()
    {
        $service = new BitBucket(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            $this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
        );

        $this->assertSame(
            'https://bitbucket.org/%21api/1.0/oauth/access_token',
            (string) $service->getAccessTokenEndpoint()
        );
    }

    /**
     * @covers OAuth\OAuth1\Service\BitBucket::__construct
     * @covers OAuth\OAuth1\Service\BitBucket::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\BitBucket::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnNulledResponse()
    {
        /** @var BitBucket|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth1\\Service\\BitBucket', ['httpRequest'], [
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
     * @covers OAuth\OAuth1\Service\BitBucket::__construct
     * @covers OAuth\OAuth1\Service\BitBucket::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\BitBucket::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnResponseNotAnArray()
    {
        /** @var BitBucket|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth1\\Service\\BitBucket', ['httpRequest'], [
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
     * @covers OAuth\OAuth1\Service\BitBucket::__construct
     * @covers OAuth\OAuth1\Service\BitBucket::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\BitBucket::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnResponseCallbackNotSet()
    {
        /** @var BitBucket|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth1\\Service\\BitBucket', ['httpRequest'], [
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
     * @covers OAuth\OAuth1\Service\BitBucket::__construct
     * @covers OAuth\OAuth1\Service\BitBucket::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\BitBucket::parseRequestTokenResponse
     */
    public function testParseRequestTokenResponseThrowsExceptionOnResponseCallbackNotTrue()
    {
	    /** @var BitBucket|\PHPUnit_Framework_MockObject_MockObject $service */
	    $service = $this->getMock('\\OAuth\\OAuth1\\Service\\BitBucket', ['httpRequest'], [
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
     * @covers OAuth\OAuth1\Service\BitBucket::__construct
     * @covers OAuth\OAuth1\Service\BitBucket::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\BitBucket::parseRequestTokenResponse
     * @covers OAuth\OAuth1\Service\BitBucket::parseAccessTokenResponse
     */
    public function testParseRequestTokenResponseValid()
    {
	    /** @var BitBucket|\PHPUnit_Framework_MockObject_MockObject $service */
	    $service = $this->getMock('\\OAuth\\OAuth1\\Service\\BitBucket', ['httpRequest'], [
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
     * @covers OAuth\OAuth1\Service\BitBucket::__construct
     * @covers OAuth\OAuth1\Service\BitBucket::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\BitBucket::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnError()
    {
        $token = $this->getMock('\\OAuth\\OAuth1\\Token\\TokenInterface');
        $storage = $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

		/** @var BitBucket|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth1\\Service\\BitBucket', ['httpRequest'], [
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
     * @covers OAuth\OAuth1\Service\BitBucket::__construct
     * @covers OAuth\OAuth1\Service\BitBucket::getRequestTokenEndpoint
     * @covers OAuth\OAuth1\Service\BitBucket::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseValid()
    {
	    $token = $this->getMock('\\OAuth\\OAuth1\\Token\\TokenInterface');
	    $storage = $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
	    $storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

	    /** @var BitBucket|\PHPUnit_Framework_MockObject_MockObject $service */
	    $service = $this->getMock('\\OAuth\\OAuth1\\Service\\BitBucket', ['httpRequest'], [
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
