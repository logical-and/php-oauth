<?php

namespace OAuthTest\Unit\OAuth2\Service;

use OAuth\Common\Token\TokenInterface;
use OAuth\OAuth2\Service\Facebook;

class FacebookTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers OAuth\OAuth2\Service\Facebook::__construct
     */
    public function testConstructCorrectInterfaceWithoutCustomUri()
    {
        $service = new Facebook(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\ServiceInterface', $service);
    }

    /**
     * @covers OAuth\OAuth2\Service\Facebook::__construct
     */
    public function testConstructCorrectInstanceWithoutCustomUri()
    {
        $service = new Facebook(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth2\Service\Facebook::__construct
     */
    public function testConstructCorrectInstanceWithCustomUri()
    {
        $service = new Facebook(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            [],
            'some-url'
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth2\Service\Facebook::__construct
     * @covers OAuth\OAuth2\Service\Facebook::getAuthorizationEndpoint
     * @group active
     */
    public function testGetAuthorizationEndpoint()
    {
        $service = new Facebook(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertSame('https://www.facebook.com/dialog/oauth', (string) $service->getAuthorizationEndpoint());
    }

    /**
     * @covers OAuth\OAuth2\Service\Facebook::__construct
     * @covers OAuth\OAuth2\Service\Facebook::getAccessTokenEndpoint
     */
    public function testGetAccessTokenEndpoint()
    {
        $service = new Facebook(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertSame('https://graph.facebook.com/oauth/access_token', (string) $service->getAccessTokenEndpoint());
    }

    /**
     * @covers OAuth\OAuth2\Service\Facebook::__construct
     */
    public function testGetAuthorizationMethod()
    {
        $token = $this->getMock('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', ['getEndOfLife', 'getAccessToken']);
        $token->expects($this->once())->method('getEndOfLife')->will(
            $this->returnValue(TokenInterface::EOL_NEVER_EXPIRES)
        );
        $token->expects($this->once())->method('getAccessToken')->will($this->returnValue('foo'));

        $storage = $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface');
        $storage->expects($this->once())->method('retrieveAccessToken')->will($this->returnValue($token));

        /** @var Facebook|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth2\\Service\\Facebook',
            ['httpRequest'],
            [
                $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->getMock('\\Buzz\\Browser'),
                $storage
            ]
        );

        $service->expects($this->once())->method('httpRequest')->will($this->returnArgument(2));

        $headers = $service->request('https://pieterhordijk.com/my/awesome/path');

        $this->assertTrue(array_key_exists('Authorization', $headers));
        $this->assertTrue(in_array('OAuth foo', $headers, true));
    }

    /**
     * @covers OAuth\OAuth2\Service\Facebook::__construct
     * @covers OAuth\OAuth2\Service\Facebook::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnError()
    {
        /** @var Facebook|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth2\\Service\\Facebook',
            ['httpRequest'],
            [
                $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->getMock('\\Buzz\\Browser'),
                $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
            ]
        );

        $service->expects($this->once())->method('httpRequest')->will($this->returnValue('error=some_error'));

        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo');
    }

    /**
     * @covers OAuth\OAuth2\Service\Facebook::__construct
     * @covers OAuth\OAuth2\Service\Facebook::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseValidWithoutRefreshToken()
    {
        /** @var Facebook|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth2\\Service\\Facebook',
            ['httpRequest'],
            [
                $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->getMock('\\Buzz\\Browser'),
                $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
            ]
        );

        $service->expects($this->once())->method('httpRequest')->will(
            $this->returnValue('access_token=foo&expires=bar')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
    }

    /**
     * @covers OAuth\OAuth2\Service\Facebook::__construct
     * @covers OAuth\OAuth2\Service\Facebook::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseValidWithRefreshToken()
    {
        /** @var Facebook|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth2\\Service\\Facebook',
            ['httpRequest'],
            [
                $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->getMock('\\Buzz\\Browser'),
                $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
            ]
        );

        $service->expects($this->once())->method('httpRequest')->will(
            $this->returnValue('access_token=foo&expires=bar&refresh_token=baz')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
    }

    /**
     * @covers OAuth\OAuth2\Service\Facebook::__construct
     * @covers OAuth\OAuth2\Service\Facebook::getDialogUri
     */
    public function testGetDialogUriRedirectUriMissing()
    {
        $client = $this->getMock('\\Buzz\\Browser');

        $service = new Facebook(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->setExpectedException('\\OAuth\\Common\\Exception\\Exception');

        $service->getDialogUri('feed', []);
    }

    /**
     * @covers OAuth\OAuth2\Service\Facebook::__construct
     * @covers OAuth\OAuth2\Service\Facebook::getDialogUri
     */
    public function testGetDialogUriInstanceofUri()
    {
        $client = $this->getMock('\\Buzz\\Browser');

        $service = new Facebook(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $client,
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $dialogUri = $service->getDialogUri(
            'feed',
            [
                'redirect_uri' => 'http://www.facebook.com',
                'state'        => 'Random state'
            ]
        );
        $this->assertInstanceOf('\\OAuth\\Common\\Http\\Url', $dialogUri);
    }

    /**
     * @covers OAuth\OAuth2\Service\Facebook::__construct
     * @covers OAuth\OAuth2\Service\Facebook::getDialogUri
     */
    public function testGetDialogUriContainsAppIdAndOtherParameters()
    {
        $client = $this->getMock('\\Buzz\\Browser');
        $credentials = $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface');
        $credentials->expects($this->any())->method('getConsumerId')->will($this->returnValue('application_id'));


        $service = new Facebook(
            $credentials,
            $client,
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $dialogUri = $service->getDialogUri(
            'feed',
            [
                'redirect_uri' => 'http://www.facebook.com',
                'state'        => 'Random state'
            ]
        );

        $queryString = $dialogUri->getQuery();
        parse_str($queryString, $queryArray);

        $this->assertArrayHasKey('app_id', $queryArray);
        $this->assertArrayHasKey('redirect_uri', $queryArray);
        $this->assertArrayHasKey('state', $queryArray);
    }
}
