<?php

namespace OAuthTest\Unit\OAuth2\Service;

use OAuth\Common\Http\Url;
use OAuth\Common\Token\TokenInterface;
use OAuth\OAuth2\Service\Mailchimp;

class MailchimpTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers OAuth\OAuth2\Service\Mailchimp::__construct
     */
    public function testConstructCorrectInterfaceWithoutCustomUri()
    {
        $service = new Mailchimp(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\ServiceInterface', $service);
    }

    /**
     * @covers OAuth\OAuth2\Service\Mailchimp::__construct
     */
    public function testConstructCorrectInstanceWithoutCustomUri()
    {
        $service = new Mailchimp(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth2\Service\Mailchimp::__construct
     */
    public function testConstructCorrectInstanceWithCustomUri()
    {
        $service = new Mailchimp(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            [],
            'some-url'
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth2\Service\Mailchimp::__construct
     * @covers OAuth\OAuth2\Service\Mailchimp::getAuthorizationEndpoint
     */
    public function testGetAuthorizationEndpoint()
    {
        $service = new Mailchimp(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertSame(
            'https://login.mailchimp.com/oauth2/authorize',
            (string) $service->getAuthorizationEndpoint()
        );
    }

    /**
     * @covers OAuth\OAuth2\Service\Mailchimp::__construct
     * @covers OAuth\OAuth2\Service\Mailchimp::getAccessTokenEndpoint
     */
    public function testGetAccessTokenEndpoint()
    {
        $service = new Mailchimp(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertSame(
            'https://login.mailchimp.com/oauth2/token',
            (string) $service->getAccessTokenEndpoint()
        );
    }

    /**
     * @covers OAuth\OAuth2\Service\Mailchimp::__construct
     * @covers OAuth\OAuth2\Service\Mailchimp::getAuthorizationMethod
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

        /** @var Mailchimp|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth2\\Service\\Mailchimp',
            ['httpRequest'],
            [
                $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->getMock('\\Buzz\\Browser'),
                $storage,
                [],
                new Url('https://us1.api.mailchimp.com/2.0/')
            ]
        );

        $service->expects($this->once())->method('httpRequest')->will($this->returnArgument(0));

        $uri = $service->request('https://pieterhordijk.com/my/awesome/path');
        $absoluteUri = parse_url((string) $uri);

        $this->assertSame('apikey=foo', $absoluteUri[ 'query' ]);
    }

    /**
     * @covers OAuth\OAuth2\Service\Mailchimp::__construct
     * @covers OAuth\OAuth2\Service\Mailchimp::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnNulledResponse()
    {
        /** @var Mailchimp|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth2\\Service\\Mailchimp',
            ['httpRequest'],
            [
                $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->getMock('\\Buzz\\Browser'),
                $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
            ]
        );

        $service->expects($this->once())->method('httpRequest')->will($this->returnValue(null));

        $this->setExpectedException('\\OAuth\\Common\\Http\\Exception\\TokenResponseException');

        $service->requestAccessToken('foo');
    }

    /**
     * @covers OAuth\OAuth2\Service\Mailchimp::__construct
     * @covers OAuth\OAuth2\Service\Mailchimp::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnError()
    {
        /** @var Mailchimp|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth2\\Service\\Mailchimp',
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
     * @covers OAuth\OAuth2\Service\Mailchimp::__construct
     * @covers OAuth\OAuth2\Service\Mailchimp::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseValid()
    {
        /** @var Mailchimp|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth2\\Service\\Mailchimp',
            ['httpRequest'],
            [
                $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->getMock('\\Buzz\\Browser'),
                $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
            ]
        );

        $service->expects($this->at(0))->method('httpRequest')->will(
            $this->returnValue('{"access_token":"foo","expires_in":"bar"}')
        );
        $service->expects($this->at(1))->method('httpRequest')->will($this->returnValue('{"dc": "us7"}'));

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
    }
}
