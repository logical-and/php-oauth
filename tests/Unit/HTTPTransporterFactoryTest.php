<?php

namespace Unit;

use Buzz\Client\FileGetContents;
use Buzz\Util\CookieJar;
use OAuth\HTTPTransporterFactory;

class HTTPTransporterFactoryTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @covers \OAuth\HTTPTransporterFactory::buildTransporter
	 * @covers \OAuth\HTTPTransporterFactory::buildClient
	 * @expectedException \OAuth\Common\Exception\Exception
	 */
	public function testBuildTransporterInvalidClassException()
	{
		HTTPTransporterFactory::buildTransporter('InvalidClient');
	}

	/**
	 * @covers \OAuth\HTTPTransporterFactory::buildTransporter
	 * @covers \OAuth\HTTPTransporterFactory::buildClient
	 * @expectedException \OAuth\Common\Exception\Exception
	 */
	public function testBuildTransporterFromInvalidClientException()
	{
		HTTPTransporterFactory::buildTransporter(new \DateTime());
	}

	/**
	 * @covers \OAuth\HTTPTransporterFactory::buildTransporter
	 * @covers \OAuth\HTTPTransporterFactory::buildClient
	 */
	public function testBuildTransporterFromString()
	{
		$browser = HTTPTransporterFactory::buildTransporter('FileGetContents');

		$this->assertInstanceOf('\Buzz\Browser', $browser);
		$this->assertInstanceOf('\Buzz\Client\FileGetContents', $browser->getClient());
	}

	/**
	 * @covers \OAuth\HTTPTransporterFactory::buildTransporter
	 * @covers \OAuth\HTTPTransporterFactory::buildClient
	 * @depends testBuildTransporterFromString
	 */
	public function testBuildTransporterFromClientObject()
	{
		$client = new FileGetContents();

		$this->assertSame(
			$client,
			HTTPTransporterFactory::buildTransporter($client)->getClient(),
			'Should be the same object'
		);
	}

	/**
	 * @covers \OAuth\HTTPTransporterFactory::buildTransporter
	 * @covers \OAuth\HTTPTransporterFactory::buildClient
	 * @depends testBuildTransporterFromString
	 */
	public function testBuildTransporterClientArguments()
	{
		// set() method
		$cookieJar = new CookieJar();
		$browser = HTTPTransporterFactory::buildTransporter('FileGetContents', [
			'cookieJar' => $cookieJar,
			'timeout' => 777
		]);

		$this->assertSame(
			$cookieJar,
			$browser->getClient()->getCookieJar(),
			'Should be the same object'
		);
		$this->assertEquals(777, $browser->getClient()->getTimeout());

		// setOption method
		$browser = HTTPTransporterFactory::buildTransporter('Curl', [
			'testOption' => 'yep'
		]);
		// Property is protected, make it public to test
		$reflProperty = new \ReflectionProperty($browser->getClient(), 'options');
		$reflProperty->setAccessible(TRUE);
		$this->assertEquals('yep', $reflProperty->getValue($browser->getClient())['testOption']);
	}
}
