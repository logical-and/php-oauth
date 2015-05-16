<?php

namespace OAuthTest\Unit\OAuth2\Service;

use OAuth\Common\Token\TokenInterface;
use OAuth\OAuth2\Service\Yammer;

class YammerTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @covers OAuth\OAuth2\Service\Yammer::__construct
	 */
	public function testConstructCorrectInterfaceWithoutCustomUri()
	{
		$service = new Yammer(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		);

		$this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\ServiceInterface', $service);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Yammer::__construct
	 */
	public function testConstructCorrectInstanceWithoutCustomUri()
	{
		$service = new Yammer(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		);

		$this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Yammer::__construct
	 */
	public function testConstructCorrectInstanceWithCustomUri()
	{
		$service = new Yammer(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
			[],
			'some-url'
		);

		$this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Yammer::__construct
	 * @covers OAuth\OAuth2\Service\Yammer::getAuthorizationEndpoint
	 */
	public function testGetAuthorizationEndpoint()
	{
		$service = new Yammer(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		);

		$this->assertSame('https://www.yammer.com/dialog/oauth', (string) $service->getAuthorizationEndpoint());
	}

	/**
	 * @covers OAuth\OAuth2\Service\Yammer::__construct
	 * @covers OAuth\OAuth2\Service\Yammer::getAccessTokenEndpoint
	 */
	public function testGetAccessTokenEndpoint()
	{
		$service = new Yammer(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		);

		$this->assertSame('https://www.yammer.com/oauth2/access_token.json', (string) $service->getAccessTokenEndpoint());
	}

	/**
	 * @covers OAuth\OAuth2\Service\Yammer::__construct
	 * @covers OAuth\OAuth2\Service\Yammer::getAuthorizationMethod
	 */
	public function testGetAuthorizationMethod()
	{
		$token = $this->getMock('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', ['getEndOfLife', 'getAccessToken']);
		$token->expects($this->once())->method('getEndOfLife')->will($this->returnValue(TokenInterface::EOL_NEVER_EXPIRES));
		$token->expects($this->once())->method('getAccessToken')->will($this->returnValue('foo'));

		$storage = $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
		$storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

		/** @var Yammer|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth2\\Service\\Yammer', ['httpRequest'], [
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
	 * @covers OAuth\OAuth2\Service\Yammer::__construct
	 * @covers OAuth\OAuth2\Service\Yammer::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseThrowsExceptionOnNulledResponse()
	{
		/** @var Yammer|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth2\\Service\\Yammer', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnValue(NULL));

		$this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

		$service->requestAccessToken('foo');
	}

	/**
	 * @covers OAuth\OAuth2\Service\Yammer::__construct
	 * @covers OAuth\OAuth2\Service\Yammer::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseThrowsExceptionOnError()
	{
		/** @var Yammer|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth2\\Service\\Yammer', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnValue('error=some_error'));

		$this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

		$service->requestAccessToken('foo');
	}

	/**
	 * @covers OAuth\OAuth2\Service\Yammer::__construct
	 * @covers OAuth\OAuth2\Service\Yammer::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseValidWithoutRefreshToken()
	{
		/** @var Yammer|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth2\\Service\\Yammer', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnValue('{"access_token":{"token":"foo", "expires_at":null}}'));

		$this->assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
	}

	/**
	 * @covers OAuth\OAuth2\Service\Yammer::__construct
	 * @covers OAuth\OAuth2\Service\Yammer::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseValidWithRefreshToken()
	{
		/** @var Yammer|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth2\\Service\\Yammer', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnValue('{"access_token":{"token":"foo", "expires_at":null},"refresh_token":"baz"}'));

		$this->assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
	}
}
