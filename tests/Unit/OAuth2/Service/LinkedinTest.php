<?php

namespace OAuthTest\Unit\OAuth2\Service;

use OAuth\Common\Token\TokenInterface;
use OAuth\OAuth2\Service\Linkedin;

class LinkedinTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @covers OAuth\OAuth2\Service\Linkedin::__construct
	 */
	public function testConstructCorrectInterfaceWithoutCustomUri()
	{
		$service = new Linkedin(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		);

		$this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\ServiceInterface', $service);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Linkedin::__construct
	 */
	public function testConstructCorrectInstanceWithoutCustomUri()
	{
		$service = new Linkedin(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		);

		$this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Linkedin::__construct
	 */
	public function testConstructCorrectInstanceWithCustomUri()
	{
		$service = new Linkedin(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
			[],
			'some-url'
		);

		$this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Linkedin::__construct
	 * @covers OAuth\OAuth2\Service\Linkedin::getAuthorizationEndpoint
	 */
	public function testGetAuthorizationEndpoint()
	{
		$service = new Linkedin(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		);

		$this->assertSame(
			'https://www.linkedin.com/uas/oauth2/authorization',
			(string) $service->getAuthorizationEndpoint()
		);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Linkedin::__construct
	 * @covers OAuth\OAuth2\Service\Linkedin::getAccessTokenEndpoint
	 */
	public function testGetAccessTokenEndpoint()
	{
		$service = new Linkedin(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		);

		$this->assertSame(
			'https://www.linkedin.com/uas/oauth2/accessToken',
			(string) $service->getAccessTokenEndpoint()
		);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Linkedin::__construct
	 * @covers OAuth\OAuth2\Service\Linkedin::getAuthorizationMethod
	 */
	public function testGetAuthorizationMethod()
	{
		$token = $this->getMock('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', ['getEndOfLife', 'getAccessToken']);
		$token->expects($this->once())->method('getEndOfLife')->will($this->returnValue(TokenInterface::EOL_NEVER_EXPIRES));
		$token->expects($this->once())->method('getAccessToken')->will($this->returnValue('foo'));

		$storage = $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
		$storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

		/** @var Linkedin|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth2\\Service\\Linkedin', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$storage
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnArgument(0));

		$uri         = $service->request('https://pieterhordijk.com/my/awesome/path');
		$absoluteUri = parse_url((string) $uri);

		$this->assertSame('oauth2_access_token=foo', $absoluteUri[ 'query' ]);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Linkedin::__construct
	 * @covers OAuth\OAuth2\Service\Linkedin::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseThrowsExceptionOnNulledResponse()
	{
		/** @var Linkedin|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth2\\Service\\Linkedin', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnValue(NULL));

		$this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

		$service->requestAccessToken('foo');
	}

	/**
	 * @covers OAuth\OAuth2\Service\Linkedin::__construct
	 * @covers OAuth\OAuth2\Service\Linkedin::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseThrowsExceptionOnErrorDescription()
	{
		/** @var Linkedin|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth2\\Service\\Linkedin', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnValue('error_description=some_error'));

		$this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

		$service->requestAccessToken('foo');
	}

	/**
	 * @covers OAuth\OAuth2\Service\Linkedin::__construct
	 * @covers OAuth\OAuth2\Service\Linkedin::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseThrowsExceptionOnError()
	{
		/** @var Linkedin|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth2\\Service\\Linkedin', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnValue('error=some_error'));

		$this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

		$service->requestAccessToken('foo');
	}

	/**
	 * @covers OAuth\OAuth2\Service\Linkedin::__construct
	 * @covers OAuth\OAuth2\Service\Linkedin::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseValidWithoutRefreshToken()
	{
		/** @var Linkedin|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth2\\Service\\Linkedin', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnValue('{"access_token":"foo","expires_in":"bar"}'));

		$this->assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
	}

	/**
	 * @covers OAuth\OAuth2\Service\Linkedin::__construct
	 * @covers OAuth\OAuth2\Service\Linkedin::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseValidWithRefreshToken()
	{
		/** @var Linkedin|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth2\\Service\\Linkedin', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnValue('{"access_token":"foo","expires_in":"bar","refresh_token":"baz"}'));

		$this->assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
	}
}
