<?php

namespace OAuthTest\Unit\OAuth2\Service;

use OAuth\Common\Token\TokenInterface;
use OAuth\OAuth2\Service\JawboneUP;

class JawboneUPTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @covers OAuth\OAuth2\Service\JawboneUP::__construct
	 */
	public function testConstructCorrectInterfaceWithoutCustomUri()
	{
		$service = new JawboneUP(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		);

		$this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\ServiceInterface', $service);
	}

	/**
	 * @covers OAuth\OAuth2\Service\JawboneUP::__construct
	 */
	public function testConstructCorrectInstanceWithoutCustomUri()
	{
		$service = new JawboneUP(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		);

		$this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
	}

	/**
	 * @covers OAuth\OAuth2\Service\JawboneUP::__construct
	 */
	public function testConstructCorrectInstanceWithCustomUri()
	{
		$service = new JawboneUP(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
			[],
			'some-url'
		);

		$this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
	}

	/**
	 * @covers OAuth\OAuth2\Service\JawboneUP::__construct
	 * @covers OAuth\OAuth2\Service\JawboneUP::getAuthorizationEndpoint
	 */
	public function testGetAuthorizationEndpoint()
	{
		$service = new JawboneUP(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		);

		$this->assertSame('https://jawbone.com/auth/oauth2/auth', (string) $service->getAuthorizationEndpoint());
	}

	/**
	 * @covers OAuth\OAuth2\Service\JawboneUP::__construct
	 * @covers OAuth\OAuth2\Service\JawboneUP::getAccessTokenEndpoint
	 */
	public function testGetAccessTokenEndpoint()
	{
		$service = new JawboneUP(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		);

		$this->assertSame('https://jawbone.com/auth/oauth2/token', (string) $service->getAccessTokenEndpoint());
	}

	/**
	 * @covers OAuth\OAuth2\Service\JawboneUP::__construct
	 * @covers OAuth\OAuth2\Service\JawboneUP::getAuthorizationMethod
	 */
	public function testGetAuthorizationMethod()
	{
		$token = $this->getMock('\\OAuth\\OAuth2\\Token\\TokenInterface');
		$token->expects($this->once())->method('getEndOfLife')->will($this->returnValue(TokenInterface::EOL_NEVER_EXPIRES));
		$token->expects($this->once())->method('getAccessToken')->will($this->returnValue('foo'));

		$storage = $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
		$storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

		/** @var JawboneUP|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth2\\Service\\JawboneUP', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$storage
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnArgument(2));

		$headers = $service->request('/users/@me');

		$this->assertTrue(array_key_exists('Authorization', $headers));
		$this->assertTrue(in_array('Bearer foo', $headers, TRUE));
	}

	/**
	 * @covers OAuth\OAuth2\Service\JawboneUP::__construct
	 * @covers OAuth\OAuth2\Service\JawboneUP::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseThrowsExceptionOnNulledResponse()
	{
		/** @var JawboneUP|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth2\\Service\\JawboneUP', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnValue(NULL));

		$this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

		$service->requestAccessToken('foo');
	}

	/**
	 * @covers OAuth\OAuth2\Service\JawboneUP::__construct
	 * @covers OAuth\OAuth2\Service\JawboneUP::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseThrowsExceptionOnErrorDescription()
	{
		/** @var JawboneUP|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth2\\Service\\JawboneUP', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnValue('error_description=some_error'));

		$this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

		$service->requestAccessToken('foo');
	}

	/**
	 * @covers OAuth\OAuth2\Service\JawboneUP::__construct
	 * @covers OAuth\OAuth2\Service\JawboneUP::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseThrowsExceptionOnError()
	{
		/** @var JawboneUP|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth2\\Service\\JawboneUP', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnValue('error=some_error'));

		$this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

		$service->requestAccessToken('foo');
	}

	/**
	 * @covers OAuth\OAuth2\Service\JawboneUP::__construct
	 * @covers OAuth\OAuth2\Service\JawboneUP::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseValidWithoutRefreshToken()
	{
		/** @var JawboneUP|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth2\\Service\\JawboneUP', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnValue('{"access_token":"foo","expires_in":"bar"}'));

		$this->assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
	}

	/**
	 * @covers OAuth\OAuth2\Service\JawboneUP::__construct
	 * @covers OAuth\OAuth2\Service\JawboneUP::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseValidWithRefreshToken()
	{
		/** @var JawboneUP|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth2\\Service\\JawboneUP', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnValue('{"access_token":"foo","expires_in":"bar","refresh_token":"baz"}'));

		$this->assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
	}
}
