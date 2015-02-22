<?php

namespace OAuthTest\Unit\OAuth1\Service;

use Buzz\Browser;
use OAuthTest\Mocks\OAuth1\Service\Mock;

class AbstractServiceTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @covers OAuth\OAuth1\Service\AbstractService::__construct
	 */
	public function testConstructCorrectInterface()
	{
		$service = $this->getMockForAbstractClass(
			'\\OAuth\\OAuth1\\Service\\AbstractService',
			[
				$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
				$this->getMock('\\Buzz\\Browser'),
				$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
				$this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
			]
		);

		$this->assertInstanceOf('\\OAuth\\OAuth1\\Service\\ServiceInterface', $service);
	}

	/**
	 * @covers OAuth\OAuth1\Service\AbstractService::__construct
	 */
	public function testConstructCorrectParent()
	{
		$service = $this->getMockForAbstractClass(
			'\\OAuth\\OAuth1\\Service\\AbstractService',
			[
				$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
				$this->getMock('\\Buzz\\Browser'),
				$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
				$this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
			]
		);

		$this->assertInstanceOf('\\OAuth\\Common\\Service\\AbstractService', $service);
	}

	/**
	 * @covers OAuth\OAuth1\Service\AbstractService::requestRequestToken
	 * @covers OAuth\OAuth1\Service\AbstractService::buildAuthorizationHeaderForTokenRequest
	 * @covers OAuth\OAuth1\Service\AbstractService::getBasicAuthorizationHeaderInfo
	 * @covers OAuth\OAuth1\Service\AbstractService::generateNonce
	 * @covers OAuth\OAuth1\Service\AbstractService::getSignatureMethod
	 * @covers OAuth\OAuth1\Service\AbstractService::getVersion
	 * @covers OAuth\OAuth1\Service\AbstractService::getExtraOAuthHeaders
	 * @covers OAuth\OAuth1\Service\AbstractService::parseRequestTokenResponse
	 */
	public function testRequestRequestTokenBuildAuthHeaderTokenRequestWithoutParams()
	{
		$service = new Mock(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			new Browser(),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
			$this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
		);

		$requestToken = $service->requestRequestToken();
		$this->assertInstanceOf('\\OAuth\\OAuth1\\Token\\StdOAuth1Token', $requestToken);
		$this->assertSame('http://pieterhordijk.com/token', (string) $requestToken->getRequestToken()['uri']);
	}

	/**
	 * @covers OAuth\OAuth1\Service\AbstractService::getAuthorizationUri
	 * @covers OAuth\OAuth1\Service\AbstractService::getAuthorizationEndpoint
	 */
	public function testGetAuthorizationUriWithoutParameters()
	{
		$service = new Mock(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
			$this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
		);

		$this->assertSame('http://pieterhordijk.com/auth', (string) $service->getAuthorizationUri([
			'oauth_token' => '' // just for skip receiving
		]));
	}

	/**
	 * @covers OAuth\OAuth1\Service\AbstractService::getAuthorizationUri
	 * @covers OAuth\OAuth1\Service\AbstractService::getAuthorizationEndpoint
	 */
	public function testGetAuthorizationUriWithParameters()
	{
		$service = new Mock(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
			$this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
		);

		$this->assertSame('http://pieterhordijk.com/auth?foo=bar&baz=beer', (string) $service->getAuthorizationUri([
			'foo' => 'bar',
			'baz' => 'beer',
			'oauth_token' => ''
		]));
	}

	/**
	 * @covers OAuth\OAuth1\Service\AbstractService::requestAccessToken
	 * @covers OAuth\OAuth1\Service\AbstractService::service
	 * @covers OAuth\OAuth1\Service\AbstractService::buildAuthorizationHeaderForAPIRequest
	 * @covers OAuth\OAuth1\Service\AbstractService::getBasicAuthorizationHeaderInfo
	 * @covers OAuth\OAuth1\Service\AbstractService::generateNonce
	 * @covers OAuth\OAuth1\Service\AbstractService::getSignatureMethod
	 * @covers OAuth\OAuth1\Service\AbstractService::getVersion
	 * @covers OAuth\OAuth1\Service\AbstractService::getAccessTokenEndpoint
	 * @covers OAuth\OAuth1\Service\AbstractService::getExtraOAuthHeaders
	 * @covers OAuth\OAuth1\Service\AbstractService::parseAccessTokenResponse
	 */
	public function testRequestAccessTokenWithoutSecret()
	{
		$token = $this->getMock('\\OAuth\\OAuth1\\Token\\TokenInterface');
		$token->expects($this->once())->method('getRequestTokenSecret')->will($this->returnValue('baz'));

		$storage = $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
		$storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new Mock(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			new Browser(),
			$storage,
			$this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
		);

		$accessToken = $service->requestAccessToken('foo', 'bar');
		$this->assertInstanceOf('\\OAuth\\OAuth1\\Token\\StdOAuth1Token', $accessToken);
		$this->assertSame('http://pieterhordijk.com/access', (string) $accessToken->getAccessToken()['uri']);
	}

	/**
	 * @covers OAuth\OAuth1\Service\AbstractService::requestAccessToken
	 * @covers OAuth\OAuth1\Service\AbstractService::service
	 * @covers OAuth\OAuth1\Service\AbstractService::buildAuthorizationHeaderForAPIRequest
	 * @covers OAuth\OAuth1\Service\AbstractService::getBasicAuthorizationHeaderInfo
	 * @covers OAuth\OAuth1\Service\AbstractService::generateNonce
	 * @covers OAuth\OAuth1\Service\AbstractService::getSignatureMethod
	 * @covers OAuth\OAuth1\Service\AbstractService::getVersion
	 * @covers OAuth\OAuth1\Service\AbstractService::getAccessTokenEndpoint
	 * @covers OAuth\OAuth1\Service\AbstractService::getExtraOAuthHeaders
	 * @covers OAuth\OAuth1\Service\AbstractService::parseAccessTokenResponse
	 */
	public function testRequestAccessTokenWithSecret()
	{
		$token = $this->getMock('\\OAuth\\OAuth1\\Token\\TokenInterface');

		$storage = $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
		$storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new Mock(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			new Browser(),
			$storage,
			$this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface')
		);

		$accessToken = $service->requestAccessToken('foo', 'bar', $token);
		$this->assertInstanceOf('\\OAuth\\OAuth1\\Token\\StdOAuth1Token', $accessToken);
		$this->assertSame('http://pieterhordijk.com/access', (string) $accessToken->getAccessToken()['uri']);
	}

	/**
	 * @covers OAuth\OAuth1\Service\AbstractService::request
	 * @covers OAuth\OAuth1\Service\AbstractService::determineRequestUriFromPath
	 * @covers OAuth\OAuth1\Service\AbstractService::service
	 * @covers OAuth\OAuth1\Service\AbstractService::getExtraApiHeaders
	 * @covers OAuth\OAuth1\Service\AbstractService::buildAuthorizationHeaderForAPIRequest
	 * @covers OAuth\OAuth1\Service\AbstractService::getBasicAuthorizationHeaderInfo
	 * @covers OAuth\OAuth1\Service\AbstractService::generateNonce
	 * @covers OAuth\OAuth1\Service\AbstractService::getSignatureMethod
	 * @covers OAuth\OAuth1\Service\AbstractService::getVersion
	 */
	public function testRequest()
	{
		$token = $this->getMock('\\OAuth\\OAuth1\\Token\\TokenInterface');
		//$token->expects($this->once())->method('getRequestTokenSecret')->will($this->returnValue('baz'));

		$storage = $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
		$storage->expects($this->any())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new Mock(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			new Browser(),
			$storage,
			$this->getMock('\\OAuth\\OAuth1\\Signature\\SignatureInterface'),
			'some'
		);

		$this->assertSame('response!', $service->request('/my/awesome/path', ['root' => 'response!'])['body']['root']);
	}
}
