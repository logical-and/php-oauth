<?php

namespace OAuthTest\Unit\OAuth2\Service;

use OAuth\Common\Token\TokenInterface;
use OAuth\OAuth2\Service\DeviantArt;

class DeviantArtTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @covers OAuth\OAuth2\Service\DeviantArt::__construct
	 */
	public function testConstructCorrectInterfaceWithoutCustomUri()
	{
		$service = new DeviantArt(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		);

		$this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\ServiceInterface', $service);
	}
	/**
	 * @covers OAuth\OAuth2\Service\DeviantArt::__construct
	 */
	public function testConstructCorrectInstanceWithoutCustomUri()
	{
		$service = new DeviantArt(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		);

		$this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
	}
	/**
	 * @covers OAuth\OAuth2\Service\DeviantArt::__construct
	 */
	public function testConstructCorrectInstanceWithCustomUri()
	{
		$service = new DeviantArt(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
			[],
			''
		);

		$this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
	}

	public function testBaseApiUriIsCorrect()
	{
		$token = $this->getMock('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', ['getEndOfLife', 'getAccessToken']);
		$token->expects($this->once())->method('getEndOfLife')->will($this->returnValue(TokenInterface::EOL_NEVER_EXPIRES));
		$token->expects($this->once())->method('getAccessToken')->will($this->returnValue('foo'));

		$storage = $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
		$storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

		/** @var DeviantArt|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth2\\Service\\DeviantArt', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$storage
		]);

		$service->expects($this->once())->method('httpRequest')->willReturnArgument(0);

		$this->assertEquals('https://www.deviantart.com/api/v1/oauth2//api/method', (string) $service->request('/api/method'));
	}

	/**
	 * @covers OAuth\OAuth2\Service\DeviantArt::__construct
	 * @covers OAuth\OAuth2\Service\DeviantArt::getAuthorizationEndpoint
	 */
	public function testGetAuthorizationEndpoint()
	{
		$service = new DeviantArt(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		);

		$this->assertSame('https://www.deviantart.com/oauth2/authorize', (string) $service->getAuthorizationEndpoint());
	}
	/**
	 * @covers OAuth\OAuth2\Service\DeviantArt::__construct
	 * @covers OAuth\OAuth2\Service\DeviantArt::getAccessTokenEndpoint
	 */
	public function testGetAccessTokenEndpoint()
	{
		$service = new DeviantArt(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		);

		$this->assertSame('https://www.deviantart.com/oauth2/token', (string) $service->getAccessTokenEndpoint());
	}
	/**
	 * @covers OAuth\OAuth2\Service\DeviantArt::__construct
	 */
	public function testGetAuthorizationMethod()
	{
		$token = $this->getMock('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', ['getEndOfLife', 'getAccessToken']);
		$token->expects($this->once())->method('getEndOfLife')->will($this->returnValue(TokenInterface::EOL_NEVER_EXPIRES));
		$token->expects($this->once())->method('getAccessToken')->will($this->returnValue('foo'));

		$storage = $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
		$storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

		/** @var DeviantArt|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth2\\Service\\DeviantArt', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$storage
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnArgument(2));

		$headers = $service->request('https://pieterhordijk.com/my/awesome/path');
		$this->assertTrue(array_key_exists('Authorization', $headers));
		$this->assertTrue(in_array('OAuth foo', $headers, true));
	}
	/**
	 * @covers OAuth\OAuth2\Service\DeviantArt::__construct
	 * @covers OAuth\OAuth2\Service\DeviantArt::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseThrowsExceptionOnError()
	{
		/** @var DeviantArt|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth2\\Service\\DeviantArt', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnValue('error=some_error'));

		$this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');
		$service->requestAccessToken('foo');
	}
	/**
	 * @covers OAuth\OAuth2\Service\DeviantArt::__construct
	 * @covers OAuth\OAuth2\Service\DeviantArt::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseValidWithoutRefreshToken()
	{
		/** @var DeviantArt|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth2\\Service\\DeviantArt', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnValue('{"access_token":"foo","expires_in":"bar"}'));

		$this->assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
	}
	/**
	 * @covers OAuth\OAuth2\Service\DeviantArt::__construct
	 * @covers OAuth\OAuth2\Service\DeviantArt::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseValidWithRefreshToken()
	{
		/** @var DeviantArt|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth2\\Service\\DeviantArt', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnValue('{"access_token":"foo","expires_in":"bar","refersh_token":"baz"}'));

		$this->assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
	}
}