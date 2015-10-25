<?php

namespace OAuthTest\Unit\Common\Service;

use OAuth\ServiceFactory;
use OAuthTest\Mocks\Common\Service\Mock;

class AbstractServiceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers OAuth\Common\Service\AbstractService::__construct
     */
    public function testConstructCorrectInterface()
    {
        $service = $this->getMockForAbstractClass(
            '\\OAuth\\Common\\Service\\AbstractService',
            [
                $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                ServiceFactory::construct()->getHttpTransporter(),
                $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
                null
            ]
        );

        $this->assertInstanceOf('\\OAuth\\Common\\Service\\ServiceInterface', $service);
    }

    /**
     * @covers OAuth\Common\Service\AbstractService::__construct
     * @covers OAuth\Common\Service\AbstractService::getStorage
     */
    public function testGetStorage()
    {
        $service = $this->getMockForAbstractClass(
            '\\OAuth\\Common\\Service\\AbstractService',
            [
                $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                ServiceFactory::construct()->getHttpTransporter(),
                $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
                null
            ]
        );

        $this->assertInstanceOf('\\OAuth\\Common\\Storage\\TokenStorageInterface', $service->getStorage());
    }

    /**
     * @covers OAuth\Common\Service\AbstractService::__construct
     * @covers OAuth\Common\Service\AbstractService::service
     */
    public function testService()
    {
        $service = new Mock(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            ServiceFactory::construct()->getHttpTransporter(),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            null
        );

        $this->assertSame('Mock', $service->service());
    }

    /**
     * @covers OAuth\Common\Service\AbstractService::__construct
     * @covers OAuth\Common\Service\AbstractService::determineRequestUriFromPath
     */
    public function testDetermineRequestUriFromPathUsingUriObject()
    {
        $service = new Mock(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            ServiceFactory::construct()->getHttpTransporter(),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            null
        );

        $this->assertInstanceOf(
            '\\OAuth\\Common\\Http\\Url',
            $service->testDetermineRequestUriFromPath($this->getMock('\\OAuth\\Common\\Http\\Url', [], [], '', false))
        );
    }

    /**
     * @covers OAuth\Common\Service\AbstractService::__construct
     * @covers OAuth\Common\Service\AbstractService::determineRequestUriFromPath
     */
    public function testDetermineRequestUriFromPathUsingHttpPath()
    {
        $service = new Mock(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            ServiceFactory::construct()->getHttpTransporter(),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            null
        );

        $uri = $service->testDetermineRequestUriFromPath('http://example.com');

        $this->assertInstanceOf('\\OAuth\\Common\\Http\\Url', $uri);
        $this->assertSame('http://example.com/', (string) $uri);
    }

    /**
     * @covers OAuth\Common\Service\AbstractService::__construct
     * @covers OAuth\Common\Service\AbstractService::determineRequestUriFromPath
     */
    public function testDetermineRequestUriFromPathUsingHttpsPath()
    {
        $service = new Mock(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            ServiceFactory::construct()->getHttpTransporter(),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            null
        );

        $uri = $service->testDetermineRequestUriFromPath('https://example.com');

        $this->assertInstanceOf('\\OAuth\\Common\\Http\\Url', $uri);
        $this->assertSame('https://example.com/', (string) $uri);
    }

    /**
     * @covers OAuth\Common\Service\AbstractService::__construct
     * @covers OAuth\Common\Service\AbstractService::determineRequestUriFromPath
     */
    public function testDetermineRequestUriFromPathThrowsExceptionOnInvalidUri()
    {
        $this->setExpectedException('\\OAuth\\Common\\Exception\\Exception');

        $service = new Mock(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            ServiceFactory::construct()->getHttpTransporter(),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            null
        );

        $uri = $service->testDetermineRequestUriFromPath('example.com');
    }

    /**
     * @covers OAuth\Common\Service\AbstractService::__construct
     * @covers OAuth\Common\Service\AbstractService::determineRequestUriFromPath
     */
    public function testDetermineRequestUriFromPathWithQueryString()
    {
        $service = new Mock(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            ServiceFactory::construct()->getHttpTransporter(),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            'https://example.com'
        );

        $uri = $service->testDetermineRequestUriFromPath(
            'path?param1=value1'
        );

        $this->assertInstanceOf('\\OAuth\\Common\\Http\\Url', $uri);
        $this->assertSame('https://example.com/path?param1=value1', (string) $uri);
    }

    /**
     * @covers OAuth\Common\Service\AbstractService::__construct
     * @covers OAuth\Common\Service\AbstractService::determineRequestUriFromPath
     */
    public function testDetermineRequestUriFromPathWithLeadingSlashInPath()
    {
        $service = new Mock(
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            ServiceFactory::construct()->getHttpTransporter(),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            'https://example.com'
        );

        $uri = $service->testDetermineRequestUriFromPath(
            '/path'
        );

        $this->assertInstanceOf('\\OAuth\\Common\\Http\\Url', $uri);
        $this->assertSame('https://example.com/path', (string) $uri);
    }

    /**
     * @covers OAuth\Common\Service\AbstractService::requestJSON
     * @expectedException \OAuth\Common\Exception\Exception
     */
    public function testRequestJSONThrowsExceptionOnWrongJSON()
    {
        $service = $this->getMockForAbstractClass(
            '\\OAuth\\Common\\Service\\AbstractService',
            [
                $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->getMock('\\Buzz\\Browser'),
                $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
                null
            ]
        );

        $service->expects($this->once())->method('request')->willReturn('error{}');

        $service->requestJSON(null);
    }

    /**
     * @covers OAuth\Common\Service\AbstractService::requestJSON
     * @expectedException \OAuth\Common\Exception\Exception
     */
    public function testRequestJSONThrowsExceptionOnNonStringValue()
    {
        $service = $this->getMockForAbstractClass(
            '\\OAuth\\Common\\Service\\AbstractService',
            [
                $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->getMock('\\Buzz\\Browser'),
                $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
                null
            ]
        );

        $service->expects($this->once())->method('request')->willReturn(true);
        $service->requestJSON(null);
    }

    /**
     * @covers OAuth\Common\Service\AbstractService::requestJSON
     */
    public function testRequestJSONDontThrowsExceptionOnJSONPrimitives()
    {
        $service = $this->getMockForAbstractClass(
            '\\OAuth\\Common\\Service\\AbstractService',
            [
                $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->getMock('\\Buzz\\Browser'),
                $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
                null
            ]
        );

        $service->expects($this->at(0))->method('request')->willReturn('true');
        $service->expects($this->at(1))->method('request')->willReturn('false');

        $this->assertEquals(true, $service->requestJSON(null));
        $this->assertEquals(false, $service->requestJSON(null));
    }

    /**
     * @covers OAuth\Common\Service\AbstractService::requestJSON
     */
    public function testRequestJSONDontThrowsExceptionOnValidJSON()
    {
        $service = $this->getMockForAbstractClass(
            '\\OAuth\\Common\\Service\\AbstractService',
            [
                $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
                $this->getMock('\\Buzz\\Browser'),
                $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
                null
            ]
        );

        $service->expects($this->once())->method('request')->willReturn('{"test":1}');

        $this->assertEquals(['test' => 1], $service->requestJSON(null));
    }
}
