<?php

namespace OAuthTest\Unit\OAuth2\Service;

use OAuth\Common\Token\TokenInterface;
use OAuth\OAuth2\Service\Spotify;

class SpotifyTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @covers OAuth\OAuth2\Service\Spotify::__construct
	 */
	public function testConstructCorrectInterfaceWithoutCustomUri()
	{
		$service = new Spotify(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		);

		$this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\ServiceInterface', $service);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Spotify::__construct
	 */
	public function testConstructCorrectInstanceWithoutCustomUri()
	{
		$service = new Spotify(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		);

		$this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Spotify::__construct
	 */
	public function testConstructCorrectInstanceWithCustomUri()
	{
		$service = new Spotify(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
			[],
			'some-url'
		);

		$this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Spotify::__construct
	 * @covers OAuth\OAuth2\Service\Spotify::getAuthorizationEndpoint
	 */
	public function testGetAuthorizationEndpoint()
	{
		$service = new Spotify(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		);

		$this->assertSame('https://accounts.spotify.com/authorize', (string) $service->getAuthorizationEndpoint());
	}

	/**
	 * @covers OAuth\OAuth2\Service\Spotify::__construct
	 * @covers OAuth\OAuth2\Service\Spotify::getAccessTokenEndpoint
	 */
	public function testGetAccessTokenEndpoint()
	{
		$service = new Spotify(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		);

		$this->assertSame('https://accounts.spotify.com/api/token', (string) $service->getAccessTokenEndpoint());
	}

	/**
	 * @covers OAuth\OAuth2\Service\Spotify::__construct
	 * @covers OAuth\OAuth2\Service\Spotify::getAuthorizationMethod
	 */
	public function testGetAuthorizationMethod()
	{
		$token = $this->getMock('\\OAuth\\OAuth2\\Token\\TokenInterface');
		$token->expects($this->once())->method('getEndOfLife')->will($this->returnValue(TokenInterface::EOL_NEVER_EXPIRES));
		$token->expects($this->once())->method('getAccessToken')->will($this->returnValue('foo'));

		$storage = $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
		$storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

		/** @var Spotify|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth2\\Service\\Spotify', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$storage
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnArgument(2));

		$headers = $service->request('https://pieterhordijk.com/my/awesome/path');

		$this->assertTrue(array_key_exists('Authorization', $headers));
		$this->assertTrue(in_array('Bearer foo', $headers, TRUE));
	}

	/**
	 * @covers OAuth\OAuth2\Service\Spotify::__construct
	 * @covers OAuth\OAuth2\Service\Spotify::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseThrowsExceptionOnNulledResponse()
	{
		/** @var Spotify|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth2\\Service\\Spotify', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnValue(NULL));

		$this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

		$service->requestAccessToken('foo');
	}

	/**
	 * @covers OAuth\OAuth2\Service\Spotify::__construct
	 * @covers OAuth\OAuth2\Service\Spotify::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseThrowsExceptionOnError()
	{
		/** @var Spotify|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth2\\Service\\Spotify', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnValue('error=some_error'));

		$this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

		$service->requestAccessToken('foo');
	}

	/**
	 * @covers OAuth\OAuth2\Service\Spotify::__construct
	 * @covers OAuth\OAuth2\Service\Spotify::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseValidWithoutRefreshToken()
	{
		/** @var Spotify|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth2\\Service\\Spotify', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnValue('{"access_token":"foo","expires_in":"bar"}'));

		$this->assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
	}

	/**
	 * @covers OAuth\OAuth2\Service\Spotify::__construct
	 * @covers OAuth\OAuth2\Service\Spotify::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseValidWithRefreshToken()
	{
		/** @var Spotify|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth2\\Service\\Spotify', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnValue('{"access_token":"foo","expires_in":"bar","refresh_token":"baz"}'));

		$this->assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
	}

	/**
	 * @covers OAuth\OAuth2\Service\Spotify::__construct
	 * @covers OAuth\OAuth2\Service\Spotify::getExtraOAuthHeaders
	 */
	public function testGetExtraOAuthHeaders()
	{
		$credentials = $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface');
		$credentials->expects($this->any())->method('getConsumerId')->will($this->returnValue('foo'));
		$credentials->expects($this->any())->method('getConsumerSecret')->will($this->returnValue('bar'));

		/** @var Spotify|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth2\\Service\\Spotify', ['httpRequest'], [
			$credentials,
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnCallback(function ($uri, $params,
			$extraHeaders)
		{
			\PHPUnit_Framework_Assert::assertTrue(array_key_exists('Authorization', $extraHeaders));
			\PHPUnit_Framework_Assert::assertSame('Basic ' . base64_encode('foo:bar'), $extraHeaders[ 'Authorization' ]);

			return '{"access_token":"foo","expires_in":"bar"}';
		}));

		$this->assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
	}
}
