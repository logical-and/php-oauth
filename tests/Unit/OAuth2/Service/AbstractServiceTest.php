<?php

namespace OAuthTest\Unit\OAuth2\Service;

use Buzz\Browser;
use OAuth\Common\Token\TokenInterface;
use OAuthTest\Mocks\OAuth2\Service\Mock;

class AbstractServiceTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @covers OAuth\OAuth2\Service\AbstractService::__construct
	 */
	public function testConstructCorrectInterface()
	{
		$service = $this->getMockForAbstractClass(
			'\\OAuth\\OAuth2\\Service\\AbstractService',
			[
				$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
				$this->getMock('\\Buzz\\Browser'),
				$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
				[],
			]
		);

		$this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\ServiceInterface', $service);
	}

	/**
	 * @covers OAuth\OAuth2\Service\AbstractService::__construct
	 */
	public function testConstructCorrectParent()
	{
		$service = $this->getMockForAbstractClass(
			'\\OAuth\\OAuth2\\Service\\AbstractService',
			[
				$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
				$this->getMock('\\Buzz\\Browser'),
				$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
				[],
			]
		);

		$this->assertInstanceOf('\\OAuth\\Common\\Service\\AbstractService', $service);
	}

	/**
	 * @covers OAuth\OAuth2\Service\AbstractService::__construct
	 */
	public function testConstructCorrectParentCustomUri()
	{
		$service = $this->getMockForAbstractClass(
			'\\OAuth\\OAuth2\\Service\\AbstractService',
			[
				$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
				$this->getMock('\\Buzz\\Browser'),
				$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
				[],
				'http://example.com',
			]
		);

		$this->assertInstanceOf('\\OAuth\\Common\\Service\\AbstractService', $service);
	}

	/**
	 * @covers OAuth\OAuth2\Service\AbstractService::__construct
	 * @covers OAuth\OAuth2\Service\AbstractService::isValidScope
	 */
	public function testConstructThrowsExceptionOnInvalidScope()
	{
		$this->setExpectedException('\\OAuth\\OAuth2\\Service\\Exception\\InvalidScopeException');

		new Mock(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
			['invalidscope']
		);
	}

	/**
	 * @covers OAuth\OAuth2\Service\AbstractService::__construct
	 * @covers OAuth\OAuth2\Service\AbstractService::getAuthorizationUri
	 * @covers OAuth\OAuth2\Service\AbstractService::getAuthorizationEndpoint
	 */
	public function testGetAuthorizationUriWithoutParametersOrScopes()
	{
		$credentials = $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface');
		$credentials->expects($this->once())->method('getConsumerId')->will($this->returnValue('foo'));
		$credentials->expects($this->once())->method('getCallbackUrl')->will($this->returnValue('bar'));

		$service = new Mock(
			$credentials,
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		);

		$this->assertSame(
			'http://pieterhordijk.com/auth?type=web_server&client_id=foo&redirect_uri=bar&response_type=code&scope=',
			(string) $service->getAuthorizationUri()
		);
	}

	/**
	 * @covers OAuth\OAuth2\Service\AbstractService::__construct
	 * @covers OAuth\OAuth2\Service\AbstractService::getAuthorizationUri
	 * @covers OAuth\OAuth2\Service\AbstractService::getAuthorizationEndpoint
	 */
	public function testGetAuthorizationUriWithParametersWithoutScopes()
	{
		$credentials = $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface');
		$credentials->expects($this->once())->method('getConsumerId')->will($this->returnValue('foo'));
		$credentials->expects($this->once())->method('getCallbackUrl')->will($this->returnValue('bar'));

		$service = new Mock(
			$credentials,
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		);

		$this->assertSame(
			'http://pieterhordijk.com/auth?type=web_server&client_id=foo&redirect_uri=bar&response_type=code&foo=bar&baz=beer&scope=',
			(string) $service->getAuthorizationUri(['foo' => 'bar', 'baz' => 'beer'])
		);
	}

	/**
	 * @covers OAuth\OAuth2\Service\AbstractService::__construct
	 * @covers OAuth\OAuth2\Service\AbstractService::isValidScope
	 * @covers OAuth\OAuth2\Service\AbstractService::getAuthorizationUri
	 * @covers OAuth\OAuth2\Service\AbstractService::getAuthorizationEndpoint
	 */
	public function testGetAuthorizationUriWithParametersAndScopes()
	{
		$credentials = $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface');
		$credentials->expects($this->once())->method('getConsumerId')->will($this->returnValue('foo'));
		$credentials->expects($this->once())->method('getCallbackUrl')->will($this->returnValue('bar'));

		$service = new Mock(
			$credentials,
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
			['mock', 'mock2']
		);

		$this->assertSame(
			'http://pieterhordijk.com/auth?type=web_server&client_id=foo&redirect_uri=bar&response_type=code&foo=bar&baz=beer&scope=mock%20mock2',
			(string) $service->getAuthorizationUri(['foo' => 'bar', 'baz' => 'beer'])
		);
	}

	/**
	 * @covers OAuth\OAuth2\Service\AbstractService::__construct
	 * @covers OAuth\OAuth2\Service\AbstractService::requestAccessToken
	 * @covers OAuth\OAuth2\Service\AbstractService::getAccessTokenEndpoint
	 * @covers OAuth\OAuth2\Service\AbstractService::getExtraOAuthHeaders
	 * @covers OAuth\OAuth2\Service\AbstractService::parseAccessTokenResponse
	 * @covers OAuth\OAuth2\Service\AbstractService::service
	 */
	public function testRequestAccessToken()
	{
		$service = new Mock(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			new Browser(),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		);

		$this->assertInstanceof('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('code'));
	}

	/**
	 * @covers OAuth\OAuth2\Service\AbstractService::__construct
	 * @covers OAuth\OAuth2\Service\AbstractService::getAuthorizationUri
	 * @covers OAuth\OAuth2\Service\AbstractService::getAuthorizationEndpoint
	 * @covers OAuth\OAuth2\Service\AbstractService::injectApiVersionToUri
	 */
	public function testGetAuthorizationUriWithInjectedVersion()
	{
		$credentials = $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface');
		$credentials->expects($this->once())->method('getConsumerId')->will($this->returnValue('foo'));
		$credentials->expects($this->once())->method('getCallbackUrl')->will($this->returnValue('bar'));

		$service = new Mock(
			$credentials,
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
			[],
			NULL,
			FALSE,
			'1.1'
		);

		$this->assertSame(
			'http://pieterhordijk.com/auth/1.1?type=web_server&client_id=foo&redirect_uri=bar&response_type=code&scope=',
			(string) $service->getAuthorizationUri()
		);
	}

	/**
	 * @covers OAuth\OAuth2\Service\AbstractService::__construct
	 * @covers OAuth\OAuth2\Service\AbstractService::getAccessTokenEndpoint
	 */
	public function testGetAccessTokenEndpointUri()
	{
		$credentials = $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface');
		$credentials->expects($this->never())->method('getConsumerId')->will($this->returnValue('foo'));
		$credentials->expects($this->never())->method('getCallbackUrl')->will($this->returnValue('bar'));

		$service = new Mock(
			$credentials,
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		);

		$this->assertSame(
			'http://pieterhordijk.com/access',
			(string) $service->getAccessTokenEndpoint()
		);
	}

	/**
	 * @covers OAuth\OAuth2\Service\AbstractService::__construct
	 * @covers OAuth\OAuth2\Service\AbstractService::getAccessTokenEndpoint
	 * @covers OAuth\OAuth2\Service\AbstractService::injectApiVersionToUri
	 */
	public function testGetAccessTokenUriWithInjectedVersion()
	{
		$credentials = $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface');
		$credentials->expects($this->never())->method('getConsumerId')->will($this->returnValue('foo'));
		$credentials->expects($this->never())->method('getCallbackUrl')->will($this->returnValue('bar'));

		$service = new Mock(
			$credentials,
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
			[],
			NULL,
			FALSE,
			'1.1'
		);

		$this->assertSame(
			'http://pieterhordijk.com/access/1.1',
			(string) $service->getAccessTokenEndpoint()
		);
	}

	/**
	 * @covers OAuth\OAuth2\Service\AbstractService::__construct
	 * @covers OAuth\OAuth2\Service\AbstractService::request
	 * @covers OAuth\OAuth2\Service\AbstractService::determineRequestUriFromPath
	 */
	public function testRequestThrowsExceptionWhenTokenIsExpired()
	{
		$tokenExpiration = new \DateTime('26-03-1984 00:00:00');

		$token = $this->getMock('\\OAuth\\OAuth2\\Token\\TokenInterface');
		$token->expects($this->any())->method('getEndOfLife')->will($this->returnValue($tokenExpiration->format('U')));

		$storage = $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
		$storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new Mock(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$storage
		);

		$this->setExpectedException('\\OAuth\\Common\\Token\\Exception\\ExpiredTokenException', 'Token expired on 03/26/1984 at 12:00:00 AM');

		$service->request('https://pieterhordijk.com/my/awesome/path');
	}

	/**
	 * @covers OAuth\OAuth2\Service\AbstractService::__construct
	 * @covers OAuth\OAuth2\Service\AbstractService::request
	 * @covers OAuth\OAuth2\Service\AbstractService::determineRequestUriFromPath
	 * @covers OAuth\OAuth2\Service\AbstractService::getAuthorizationMethod
	 * @covers OAuth\OAuth2\Service\AbstractService::parseAccessTokenResponse
	 * @covers OAuth\OAuth2\Service\AbstractService::service
	 * @covers OAuth\OAuth2\Service\AbstractService::getExtraApiHeaders
	 */
	public function testRequestOauthAuthorizationMethod()
	{
		$token = $this->getMock('\\OAuth\\OAuth2\\Token\\TokenInterface');
		$token->expects($this->once())->method('getEndOfLife')->will($this->returnValue(TokenInterface::EOL_NEVER_EXPIRES));
		$token->expects($this->once())->method('getAccessToken')->will($this->returnValue('foo'));

		$storage = $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
		$storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new Mock(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			new Browser(),
			$storage
		);

		$service->setAuthorizationMethod(Mock::AUTHORIZATION_METHOD_HEADER_OAUTH);

		$requestCopy = $service->request('https://pieterhordijk.com/my/awesome/path');
		$headers     = !empty($requestCopy[ 'headers' ]) ? $requestCopy[ 'headers' ] : [];

		$this->assertTrue(array_key_exists('Authorization', $headers));
		$this->assertTrue(in_array('OAuth foo', $headers, TRUE));
	}

	/**
	 * @covers OAuth\OAuth2\Service\AbstractService::__construct
	 * @covers OAuth\OAuth2\Service\AbstractService::request
	 * @covers OAuth\OAuth2\Service\AbstractService::determineRequestUriFromPath
	 * @covers OAuth\OAuth2\Service\AbstractService::getAuthorizationMethod
	 * @covers OAuth\OAuth2\Service\AbstractService::parseAccessTokenResponse
	 * @covers OAuth\OAuth2\Service\AbstractService::service
	 * @covers OAuth\OAuth2\Service\AbstractService::getExtraApiHeaders
	 */
	public function testRequestQueryStringMethod()
	{
		$token = $this->getMock('\\OAuth\\OAuth2\\Token\\TokenInterface');
		$token->expects($this->once())->method('getEndOfLife')->will($this->returnValue(TokenInterface::EOL_NEVER_EXPIRES));
		$token->expects($this->once())->method('getAccessToken')->will($this->returnValue('foo'));

		$storage = $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
		$storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new Mock(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			new Browser(),
			$storage
		);

		$service->setAuthorizationMethod(Mock::AUTHORIZATION_METHOD_QUERY_STRING);

		$requestData = $service->request('https://pieterhordijk.com/my/awesome/path');
		$absoluteUri = parse_url($requestData[ 'uri' ]);

		$this->assertSame('access_token=foo', $absoluteUri[ 'query' ]);
	}

	/**
	 * @covers OAuth\OAuth2\Service\AbstractService::__construct
	 * @covers OAuth\OAuth2\Service\AbstractService::request
	 * @covers OAuth\OAuth2\Service\AbstractService::determineRequestUriFromPath
	 * @covers OAuth\OAuth2\Service\AbstractService::getAuthorizationMethod
	 * @covers OAuth\OAuth2\Service\AbstractService::parseAccessTokenResponse
	 * @covers OAuth\OAuth2\Service\AbstractService::service
	 * @covers OAuth\OAuth2\Service\AbstractService::getExtraApiHeaders
	 */
	public function testRequestQueryStringTwoMethod()
	{
		$token = $this->getMock('\\OAuth\\OAuth2\\Token\\TokenInterface');
		$token->expects($this->once())->method('getEndOfLife')->will($this->returnValue(TokenInterface::EOL_NEVER_EXPIRES));
		$token->expects($this->once())->method('getAccessToken')->will($this->returnValue('foo'));

		$storage = $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
		$storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new Mock(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			new Browser(),
			$storage
		);

		$service->setAuthorizationMethod(Mock::AUTHORIZATION_METHOD_QUERY_STRING_V2);

		$requestData = $service->request('https://pieterhordijk.com/my/awesome/path');
		$absoluteUri = parse_url($requestData[ 'uri' ]);


		$this->assertSame('oauth2_access_token=foo', $absoluteUri[ 'query' ]);
	}

	/**
	 * @covers OAuth\OAuth2\Service\AbstractService::__construct
	 * @covers OAuth\OAuth2\Service\AbstractService::request
	 * @covers OAuth\OAuth2\Service\AbstractService::determineRequestUriFromPath
	 * @covers OAuth\OAuth2\Service\AbstractService::getAuthorizationMethod
	 * @covers OAuth\OAuth2\Service\AbstractService::parseAccessTokenResponse
	 * @covers OAuth\OAuth2\Service\AbstractService::service
	 * @covers OAuth\OAuth2\Service\AbstractService::getExtraApiHeaders
	 */
	public function testRequestBearerMethod()
	{
		$token = $this->getMock('\\OAuth\\OAuth2\\Token\\TokenInterface');
		$token->expects($this->once())->method('getEndOfLife')->will($this->returnValue(TokenInterface::EOL_NEVER_EXPIRES));
		$token->expects($this->once())->method('getAccessToken')->will($this->returnValue('foo'));

		$storage = $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
		$storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

		$service = new Mock(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			new Browser,
			$storage
		);

		$service->setAuthorizationMethod(Mock::AUTHORIZATION_METHOD_HEADER_BEARER);

		$headers = $service->request('https://pieterhordijk.com/my/awesome/path')[ 'headers' ];

		$this->assertTrue(array_key_exists('Authorization', $headers));
		$this->assertTrue(in_array('Bearer foo', $headers, TRUE));
	}

	/**
	 * @covers OAuth\OAuth2\Service\AbstractService::__construct
	 * @covers OAuth\OAuth2\Service\AbstractService::getStorage
	 */
	public function testGetStorage()
	{
		$service = new Mock(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		);

		$this->assertInstanceOf('\\OAuth\\Common\\Storage\\TokenStorageInterface', $service->getStorage());
	}

	/**
	 * @covers OAuth\OAuth2\Service\AbstractService::__construct
	 * @covers OAuth\OAuth2\Service\AbstractService::refreshAccessToken
	 * @covers OAuth\OAuth2\Service\AbstractService::getAccessTokenEndpoint
	 * @covers OAuth\OAuth2\Service\AbstractService::getExtraOAuthHeaders
	 * @covers OAuth\OAuth2\Service\AbstractService::parseAccessTokenResponse
	 */
	public function testRefreshAccessTokenSuccess()
	{
		$service = new Mock(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		);

		$token = $this->getMock('\\OAuth\\OAuth2\\Token\\StdOAuth2Token');
		$token->expects($this->once())->method('getRefreshToken')->will($this->returnValue('foo'));

		$this->assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->refreshAccessToken($token));
	}

	/**
	 * @covers OAuth\OAuth2\Service\AbstractService::__construct
	 * @covers OAuth\OAuth2\Service\AbstractService::isValidScope
	 */
	public function testIsValidScopeTrue()
	{
		$service = new Mock(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		);

		$this->assertTrue($service->isValidScope('mock'));
	}

	/**
	 * @covers OAuth\OAuth2\Service\AbstractService::__construct
	 * @covers OAuth\OAuth2\Service\AbstractService::isValidScope
	 */
	public function testIsValidScopeFalse()
	{
		$service = new Mock(
			$this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
			$this->getMock('\\Buzz\\Browser'),
			$this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
		);

		$this->assertFalse($service->isValidScope('invalid'));
	}
}
