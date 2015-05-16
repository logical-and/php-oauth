<?php

namespace OAuthTest\Unit\OAuth2\Service;

use OAuth\OAuth2\Service\Vkontakte;

class VkontakteTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers OAuth\OAuth2\Service\Vkontakte::__construct
     */
    public function testConstructCorrectInterfaceWithoutCustomUri()
    {
        $service = new Vkontakte(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\ServiceInterface', $service);
    }

    /**
     * @covers OAuth\OAuth2\Service\Vkontakte::__construct
     */
    public function testConstructCorrectInstanceWithoutCustomUri()
    {
        $service = new Vkontakte(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth2\Service\Vkontakte::__construct
     */
    public function testConstructCorrectInstanceWithCustomUri()
    {
        $service = new Vkontakte(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            [],
            'some-url'
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\AbstractService', $service);
    }

    /**
     * @covers OAuth\OAuth2\Service\Vkontakte::__construct
     * @covers OAuth\OAuth2\Service\Vkontakte::getAuthorizationEndpoint
     */
    public function testGetAuthorizationEndpoint()
    {
        $service = new Vkontakte(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertSame('https://oauth.vk.com/authorize', (string) $service->getAuthorizationEndpoint());
    }

    /**
     * @covers OAuth\OAuth2\Service\Vkontakte::__construct
     * @covers OAuth\OAuth2\Service\Vkontakte::getAccessTokenEndpoint
     */
    public function testGetAccessTokenEndpoint()
    {
        $service = new Vkontakte(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\Buzz\\Browser'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertSame('https://oauth.vk.com/access_token', (string) $service->getAccessTokenEndpoint());
    }

    /**
     * @covers OAuth\OAuth2\Service\Vkontakte::__construct
     * @covers OAuth\OAuth2\Service\Vkontakte::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnNulledResponse()
    {
        /** @var Vkontakte|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth2\\Service\\Vkontakte',
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
     * @covers OAuth\OAuth2\Service\Vkontakte::__construct
     * @covers OAuth\OAuth2\Service\Vkontakte::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseThrowsExceptionOnError()
    {
        /** @var Vkontakte|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth2\\Service\\Vkontakte',
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
     * @covers OAuth\OAuth2\Service\Vkontakte::__construct
     * @covers OAuth\OAuth2\Service\Vkontakte::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseValidWithoutRefreshToken()
    {
        /** @var Vkontakte|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth2\\Service\\Vkontakte',
            ['httpRequest'],
            [
                $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->getMock('\\Buzz\\Browser'),
                $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
            ]
        );

        $service->expects($this->once())->method('httpRequest')->will(
            $this->returnValue('{"access_token":"foo","expires_in":"bar"}')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
    }

    /**
     * @covers OAuth\OAuth2\Service\Vkontakte::__construct
     * @covers OAuth\OAuth2\Service\Vkontakte::parseAccessTokenResponse
     */
    public function testParseAccessTokenResponseValidWithRefreshToken()
    {
        /** @var Vkontakte|\PHPUnit_Framework_MockObject_MockObject $service */
        $service = $this->getMock(
            '\\OAuth\\OAuth2\\Service\\Vkontakte',
            ['httpRequest'],
            [
                $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->getMock('\\Buzz\\Browser'),
                $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
            ]
        );

        $service->expects($this->once())->method('httpRequest')->will(
            $this->returnValue('{"access_token":"foo","expires_in":"bar","refresh_token":"baz"}')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Token\\StdOAuth2Token', $service->requestAccessToken('foo'));
    }
}
