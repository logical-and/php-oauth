<?php

namespace OAuthTest\Unit\Common\Http;

use OAuth\Common\Http\Url;

class UrlTest extends \PHPUnit_Framework_TestCase {

	public function testConstruct()
	{
		$url = new Url('https://usr:pwd@domain:81/papath?query=value#frag');

		$this->assertInstanceOf('\\OAuth\\Common\\Http\\Url', $url);
		$this->assertEquals([
			'scheme' => 'https',
			'user' => 'usr',
			'pass' => 'pwd',
			'host' => 'domain',
			'port' => '81',
			'path' => 'papath',
			'query' => 'query=value',
			'fragment' => 'frag'
		], $url->toArray());
	}

	public function testClone()
	{
		$url = new Url('http://domain');
		$clonedUrl = clone $url;

		$this->assertInstanceOf('\\OAuth\\Common\\Http\\Url', $clonedUrl,
			'Same class');

		$this->assertEquals('domain', $url->getHost()->get());
		$this->assertEquals('domain', $clonedUrl->getHost()->get());

		// Change value
		$url->getHost()->set('another-domain');

		// Value in original url is changed
		$this->assertEquals('another-domain', $url->getHost()->get());
		// Value in clone url not changed
		$this->assertEquals('domain', $clonedUrl->getHost()->get());
	}
}