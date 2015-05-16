<?php

namespace OAuthTest\Unit\OAuth2\Service;

use OAuth\Common\Token\TokenInterface;
use OAuth\OAuth2\Service\Dropbox;

class DropboxTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @covers OAuth\OAuth2\Service\Dropbox::__construct
	 */
	public function testConstructCorrectInterfaceWithoutCustomUri()
	{
		$service = new Dropbox(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		);

		$this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\ServiceInterface', $service);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Dropbox::__construct
	 */
	public function testConstructCorrectInstanceWithoutCustomUri()
	{
		$service = new Dropbox(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		);

		$this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Dropbox::__construct
	 */
	public function testConstructCorrectInstanceWithCustomUri()
	{
		$service = new Dropbox(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
			[],
			'some-url'
		);

		$this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Dropbox::__construct
	 * @covers OAuth\OAuth2\Service\Dropbox::getAuthorizationUri
	 */
	public function testGetAuthorizationUriWithoutAdditionalParams()
	{
		$credentials = $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface');
		$credentials->expects($this->once())->method('getConsumerId')->will($this->returnValue('foo'));
		$credentials->expects($this->once())->method('getCallbackUrl')->will($this->returnValue('bar'));

		$service = new Dropbox(
			$credentials,
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		);

		$this->assertSame(
			'https://www.dropbox.com/1/oauth2/authorize?client_id=foo&redirect_uri=bar&response_type=code&scope=',
			(string) $service->getAuthorizationUri()
		);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Dropbox::__construct
	 * @covers OAuth\OAuth2\Service\Dropbox::getAuthorizationUri
	 */
	public function testGetAuthorizationUriWithAdditionalParams()
	{
		$credentials = $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface');
		$credentials->expects($this->once())->method('getConsumerId')->will($this->returnValue('foo'));
		$credentials->expects($this->once())->method('getCallbackUrl')->will($this->returnValue('bar'));

		$service = new Dropbox(
			$credentials,
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		);

		$this->assertSame(
			'https://www.dropbox.com/1/oauth2/authorize?client_id=foo&redirect_uri=bar&response_type=code&scope=',
			(string) $service->getAuthorizationUri()
		);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Dropbox::__construct
	 * @covers OAuth\OAuth2\Service\Dropbox::getAuthorizationEndpoint
	 */
	public function testGetAuthorizationEndpoint()
	{
		$service = new Dropbox(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		);

		$this->assertSame('https://www.dropbox.com/1/oauth2/authorize', (string) $service->getAuthorizationEndpoint());
	}

	/**
	 * @covers OAuth\OAuth2\Service\Dropbox::__construct
	 * @covers OAuth\OAuth2\Service\Dropbox::getAccessTokenEndpoint
	 */
	public function testGetAccessTokenEndpoint()
	{
		$service = new Dropbox(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		);

		$this->assertSame('https://api.dropbox.com/1/oauth2/token', (string) $service->getAccessTokenEndpoint());
	}

	/**
	 * @covers OAuth\OAuth2\Service\Dropbox::__construct
	 * @covers OAuth\OAuth2\Service\Dropbox::getAuthorizationMethod
	 */
	public function testGetAuthorizationMethod()
	{
		$token = $this->getMock('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', ['getEndOfLife', 'getAccessToken']);
		$token->expects($this->once())->method('getEndOfLife')->will($this->returnValue(TokenInterface::EOL_NEVER_EXPIRES));
		$token->expects($this->once())->method('getAccessToken')->will($this->returnValue('foo'));

		$storage = $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
		$storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

		/** @var Dropbox|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth2\\Service\\Dropbox', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$storage
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnArgument(0));

		$uri         = $service->request('https://pieterhordijk.com/my/awesome/path');
		$absoluteUri = parse_url((string) $uri);

		$this->assertSame('access_token=foo', $absoluteUri[ 'query' ]);
	}

	/**
	 * @covers OAuth\OAuth2\Service\Dropbox::__construct
	 * @covers OAuth\OAuth2\Service\Dropbox::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseThrowsExceptionOnNulledResponse()
	{
		/** @var Dropbox|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth2\\Service\\Dropbox', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnValue(NULL));

		$this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

		$service->requestAccessToken('foo');
	}

	/**
	 * @covers OAuth\OAuth2\Service\Dropbox::__construct
	 * @covers OAuth\OAuth2\Service\Dropbox::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseThrowsExceptionOnError()
	{
		/** @var Dropbox|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth2\\Service\\Dropbox', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnValue('error=some_error'));

		$this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

		$service->requestAccessToken('foo');
	}

	/**
	 * @covers OAuth\OAuth2\Service\Dropbox::__construct
	 * @covers OAuth\OAuth2\Service\Dropbox::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseValidWithoutRefreshToken()
	{
		/** @var Dropbox|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth2\\Service\\Dropbox', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnValue('{"access_token":"foo","expires_in":"bar"}'));

		$this->assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
	}

	/**
	 * @covers OAuth\OAuth2\Service\Dropbox::__construct
	 * @covers OAuth\OAuth2\Service\Dropbox::parseAccessTokenResponse
	 */
	public function testParseAccessTokenResponseValidWithRefreshToken()
	{
		/** @var Dropbox|\PHPUnit_Framework_MockObject_MockObject $service */
		$service = $this->getMock('\\OAuth\\OAuth2\\Service\\Dropbox', ['httpRequest'], [
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		]);

		$service->expects($this->once())->method('httpRequest')->will($this->returnValue('{"access_token":"foo","expires_in":"bar","refresh_token":"baz"}'));

		$this->assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
	}
}
