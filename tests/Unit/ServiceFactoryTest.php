<?php
/**
 * @category   OAuth
 * @package    Tests
 * @author     David Desberg <david@daviddesberg.com>
 * @author     Chris Heng <bigblah@gmail.com>
 * @author     Pieter Hordijk <info@pieterhordijk.com>
 * @copyright  Copyright (c) 2013 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */
namespace OAuth\Unit;

use Buzz\Browser;
use Buzz\Client\FileGetContents;
use OAuth\ServiceFactory;

class ServiceFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers OAuth\ServiceFactory::construct()
     */
    public function testConstruct()
    {
        $this->assertInstanceOf(
            '\\OAuth\\ServiceFactory',
            ServiceFactory::construct()
        );
    }

    /**
     * @covers OAuth\ServiceFactory::setHttpTransporter()
     * @covers OAuth\ServiceFactory::getHttpTransporter()
     */
    public function testGetDefaultHttpTransporter()
    {
        $serviceFactory = ServiceFactory::construct();

        $this->assertInstanceOf(
            '\\Buzz\\Browser',
            $serviceFactory->getHttpTransporter(),
            'Should return default Browser'
        );
        $this->assertInstanceOf(
            '\\Buzz\\Client\\FileGetContents',
            $serviceFactory->getHttpTransporter()->getClient(),
            'Should return default Client'
        );
    }

    /**
     * @covers OAuth\ServiceFactory::setHttpTransporter()
     * @covers OAuth\ServiceFactory::getHttpTransporter()
     */
    public function testSetHttpTransporter()
    {
        $serviceFactory = ServiceFactory::construct();

        $newClient = new FileGetContents();
        $newBrowser = new Browser($newClient);
        $serviceFactory->setHttpTransporter($newBrowser);

        $this->assertSame(
            $newBrowser,
            $serviceFactory->getHttpTransporter(),
            'Should return new Browser'
        );
        $this->assertSame(
            $newClient,
            $serviceFactory->getHttpTransporter()->getClient(),
            'Should return new Client'
        );
    }

    /**
     * @covers OAuth\ServiceFactory::registerService
     */
    public function testRegisterServiceThrowsExceptionNonExistentClass()
    {
        $this->setExpectedException('\\OAuth\Common\Exception\Exception');

        ServiceFactory::construct()->registerService('foo', 'bar');
    }

    /**
     * @covers OAuth\ServiceFactory::registerService
     */
    public function testRegisterServiceThrowsExceptionWithClassIncorrectImplementation()
    {
        $this->setExpectedException('\\OAuth\Common\Exception\Exception');

        ServiceFactory::construct()->registerService('foo', 'OAuth\\ServiceFactory');
    }

    /**
     * @covers OAuth\ServiceFactory::registerService
     */
    public function testRegisterServiceSuccessOAuth1()
    {
        $this->assertInstanceOf(
            '\\OAuth\\ServiceFactory',
            ServiceFactory::construct()->registerService('foo', '\\OAuthTest\\Mocks\\OAuth1\\Service\\Fake')
        );
    }

    /**
     * @covers OAuth\ServiceFactory::registerService
     */
    public function testRegisterServiceSuccessOAuth2()
    {
        $this->assertInstanceOf(
            '\\OAuth\\ServiceFactory',
            ServiceFactory::construct()->registerService('foo', '\\OAuthTest\\Mocks\\OAuth2\\Service\\Fake')
        );
    }

    /**
     * @covers OAuth\ServiceFactory::createService
     * @covers OAuth\ServiceFactory::getFullyQualifiedServiceName
     * @covers OAuth\ServiceFactory::buildV1Service
     */
    public function testCreateServiceOAuth1NonRegistered()
    {
        $service = ServiceFactory::construct()->createService(
            'twitter',
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Service\\Twitter', $service);
    }

    /**
     * @covers OAuth\ServiceFactory::registerService
     * @covers OAuth\ServiceFactory::createService
     * @covers OAuth\ServiceFactory::getFullyQualifiedServiceName
     * @covers OAuth\ServiceFactory::buildV1Service
     */
    public function testCreateServiceOAuth1Registered()
    {
        $factory = new ServiceFactory();

        $factory->registerService('foo', '\\OAuthTest\\Mocks\\OAuth1\\Service\\Fake');

        $service = $factory->createService(
            'foo',
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\OAuth1\Service\\ServiceInterface', $service);
        $this->assertInstanceOf('\\OAuthTest\\Mocks\\OAuth1\\Service\\Fake', $service);
    }

    /**
     * @covers OAuth\ServiceFactory::registerService
     * @covers OAuth\ServiceFactory::createService
     * @covers OAuth\ServiceFactory::getFullyQualifiedServiceName
     * @covers OAuth\ServiceFactory::buildV1Service
     */
    public function testCreateServiceOAuth1RegisteredAndNonRegisteredSameName()
    {
        $factory = new ServiceFactory();

        $factory->registerService('twitter', '\\OAuthTest\\Mocks\\OAuth1\\Service\\Fake');

        $service = $factory->createService(
            'twitter',
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\OAuth1\Service\\ServiceInterface', $service);
        $this->assertInstanceOf('\\OAuthTest\\Mocks\\OAuth1\\Service\\Fake', $service);
    }

    /**
     * @covers OAuth\ServiceFactory::createService
     * @covers OAuth\ServiceFactory::getFullyQualifiedServiceName
     * @covers OAuth\ServiceFactory::buildV2Service
     * @covers OAuth\ServiceFactory::resolveScopes
     */
    public function testCreateServiceOAuth2NonRegistered()
    {
        $factory = new ServiceFactory();

        $service = $factory->createService(
            'facebook',
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\\OAuth2\\Service\\Facebook', $service);
    }

    /**
     * @covers OAuth\ServiceFactory::createService
     * @covers OAuth\ServiceFactory::getFullyQualifiedServiceName
     * @covers OAuth\ServiceFactory::buildV2Service
     * @covers OAuth\ServiceFactory::resolveScopes
     */
    public function testCreateServiceOAuth2Registered()
    {
        $factory = new ServiceFactory();

        $factory->registerService('foo', '\\OAuthTest\\Mocks\\OAuth2\\Service\\Fake');

        $service = $factory->createService(
            'foo',
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\OAuth2\Service\\ServiceInterface', $service);
        $this->assertInstanceOf('\\OAuthTest\\Mocks\\OAuth2\\Service\\Fake', $service);
    }

    /**
     * @covers OAuth\ServiceFactory::createService
     * @covers OAuth\ServiceFactory::getFullyQualifiedServiceName
     * @covers OAuth\ServiceFactory::buildV2Service
     * @covers OAuth\ServiceFactory::resolveScopes
     */
    public function testCreateServiceOAuth2RegisteredAndNonRegisteredSameName()
    {
        $factory = new ServiceFactory();

        $factory->registerService('facebook', '\\OAuthTest\\Mocks\\OAuth2\\Service\\Fake');

        $service = $factory->createService(
            'facebook',
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\OAuth2\Service\\ServiceInterface', $service);
        $this->assertInstanceOf('\\OAuthTest\\Mocks\\OAuth2\\Service\\Fake', $service);
    }

    /**
     * @covers OAuth\ServiceFactory::registerService
     * @covers OAuth\ServiceFactory::createService
     * @covers OAuth\ServiceFactory::getFullyQualifiedServiceName
     * @covers OAuth\ServiceFactory::buildV1Service
     */
    public function testCreateServiceThrowsExceptionOnPassingScopesToV1Service()
    {
        $this->setExpectedException('\\OAuth\Common\Exception\Exception');

        $factory = new ServiceFactory();

        $factory->registerService('foo', '\\OAuthTest\\Mocks\\OAuth1\\Service\\Fake');

        $service = $factory->createService(
            'foo',
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            ['bar']
        );
    }

    /**
     * @covers OAuth\ServiceFactory::createService
     * @covers OAuth\ServiceFactory::getFullyQualifiedServiceName
     */
    public function testCreateServiceNonExistentService()
    {
        $factory = new ServiceFactory();

        $service = $factory->createService(
            'foo',
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertNull($service);
    }

    /**
     * @covers OAuth\ServiceFactory::registerService
     * @covers OAuth\ServiceFactory::createService
     * @covers OAuth\ServiceFactory::getFullyQualifiedServiceName
     * @covers OAuth\ServiceFactory::buildV2Service
     * @covers OAuth\ServiceFactory::resolveScopes
     */
    public function testCreateServicePrefersOauth2()
    {
        $factory = new ServiceFactory();

        $factory->registerService('foo', '\\OAuthTest\\Mocks\\OAuth2\\Service\\Fake');
        $factory->registerService('foo', '\\OAuthTest\\Mocks\\OAuth1\\Service\\Fake');

        $service = $factory->createService(
            'foo',
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface')
        );

        $this->assertInstanceOf('\\OAuth\OAuth2\Service\\ServiceInterface', $service);
        $this->assertInstanceOf('\\OAuthTest\\Mocks\\OAuth2\\Service\\Fake', $service);
    }

    /**
     * @covers OAuth\ServiceFactory::createService
     * @covers OAuth\ServiceFactory::getFullyQualifiedServiceName
     * @covers OAuth\ServiceFactory::buildV2Service
     * @covers OAuth\ServiceFactory::resolveScopes
     */
    public function testCreateServiceOAuth2RegisteredWithClassConstantsAsScope()
    {
        $factory = new ServiceFactory();

        $factory->registerService('foo', '\\OAuthTest\\Mocks\\OAuth2\\Service\\Fake');

        $service = $factory->createService(
            'foo',
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            ['FOO']
        );

        $this->assertInstanceOf('\\OAuth\OAuth2\Service\\ServiceInterface', $service);
        $this->assertInstanceOf('\\OAuthTest\\Mocks\\OAuth2\\Service\\Fake', $service);
    }

    /**
     * @covers OAuth\ServiceFactory::createService
     * @covers OAuth\ServiceFactory::getFullyQualifiedServiceName
     * @covers OAuth\ServiceFactory::buildV2Service
     * @covers OAuth\ServiceFactory::resolveScopes
     */
    public function testCreateServiceOAuth2RegisteredWithCustomScope()
    {
        $factory = new ServiceFactory();

        $factory->registerService('foo', '\\OAuthTest\\Mocks\\OAuth2\\Service\\Fake');

        $service = $factory->createService(
            'foo',
            $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'),
            $this->getMock('\\OAuth\\Common\\Storage\\TokenStorageInterface'),
            ['some']
        );

        $this->assertInstanceOf('\\OAuth\OAuth2\Service\\ServiceInterface', $service);
        $this->assertInstanceOf('\\OAuthTest\\Mocks\\OAuth2\\Service\\Fake', $service);
    }
}
