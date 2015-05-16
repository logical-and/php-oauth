<?php

namespace OAuthTest\Unit\OAuth1\Signature;

use OAuth\Common\Http\Url;
use OAuth\OAuth1\Signature\Signature;

class SignatureTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers OAuth\OAuth1\Signature\Signature::__construct
     */
    public function testConstructCorrectInterface()
    {
        $signature = new Signature($this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'));

        $this->assertInstanceOf('\\OAuth\\OAuth1\\Signature\\SignatureInterface', $signature);
    }

    /**
     * @covers OAuth\OAuth1\Signature\Signature::__construct
     * @covers OAuth\OAuth1\Signature\Signature::setHashingAlgorithm
     */
    public function testSetHashingAlgorithm()
    {
        $signature = new Signature($this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'));

        $this->assertNull($signature->setHashingAlgorithm('foo'));
    }

    /**
     * @covers OAuth\OAuth1\Signature\Signature::__construct
     * @covers OAuth\OAuth1\Signature\Signature::setTokenSecret
     */
    public function testSetTokenSecret()
    {
        $signature = new Signature($this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface'));

        $this->assertNull($signature->setTokenSecret('foo'));
    }

    /**
     * @covers OAuth\OAuth1\Signature\Signature::__construct
     * @covers OAuth\OAuth1\Signature\Signature::setHashingAlgorithm
     * @covers OAuth\OAuth1\Signature\Signature::setTokenSecret
     * @covers OAuth\OAuth1\Signature\Signature::getSignature
     * @covers OAuth\OAuth1\Signature\Signature::buildSignatureDataString
     * @covers OAuth\OAuth1\Signature\Signature::hash
     * @covers OAuth\OAuth1\Signature\Signature::getSigningKey
     */
    public function testGetSignatureBareUri()
    {
        $credentials = $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface');
        $credentials->expects($this->any())
            ->method('getConsumerSecret')
            ->will($this->returnValue('foo'));


        $signature = new Signature($credentials);

        $signature->setHashingAlgorithm('HMAC-SHA1');
        $signature->setTokenSecret('foo');

        $uri = new Url('http://nohost/foo');
        $this->assertSame('WNfrAIWZNwvVOsdIRSUpbW+gFNU=', $signature->getSignature($uri, ['pee' => 'haa']));
    }

    /**
     * @covers OAuth\OAuth1\Signature\Signature::__construct
     * @covers OAuth\OAuth1\Signature\Signature::setHashingAlgorithm
     * @covers OAuth\OAuth1\Signature\Signature::setTokenSecret
     * @covers OAuth\OAuth1\Signature\Signature::getSignature
     * @covers OAuth\OAuth1\Signature\Signature::buildSignatureDataString
     * @covers OAuth\OAuth1\Signature\Signature::hash
     * @covers OAuth\OAuth1\Signature\Signature::getSigningKey
     */
    public function testGetSignatureWithQueryString()
    {
        $credentials = $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface');
        $credentials->expects($this->any())
            ->method('getConsumerSecret')
            ->will($this->returnValue('foo'));


        $signature = new Signature($credentials);

        $signature->setHashingAlgorithm('HMAC-SHA1');
        $signature->setTokenSecret('foo');

        $uri = new Url('http://nohost/foo?param1=value1');
        $this->assertSame('FEneLE5E+geXq22HGt8MAsiKcyw=', $signature->getSignature($uri, ['pee' => 'haa']));
    }

    /**
     * @covers OAuth\OAuth1\Signature\Signature::__construct
     * @covers OAuth\OAuth1\Signature\Signature::setHashingAlgorithm
     * @covers OAuth\OAuth1\Signature\Signature::setTokenSecret
     * @covers OAuth\OAuth1\Signature\Signature::getSignature
     * @covers OAuth\OAuth1\Signature\Signature::buildSignatureDataString
     * @covers OAuth\OAuth1\Signature\Signature::hash
     * @covers OAuth\OAuth1\Signature\Signature::getSigningKey
     */
    public function testGetSignatureWithAuthority()
    {
        $credentials = $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface');
        $credentials->expects($this->any())
            ->method('getConsumerSecret')
            ->will($this->returnValue('foo'));


        $signature = new Signature($credentials);

        $signature->setHashingAlgorithm('HMAC-SHA1');
        $signature->setTokenSecret('foo');

        $uri = new Url('http://peehaa:pass@nohost/foo?param1=value1');
        $this->assertSame('hu/okdJo2nuzD/oblruh1QV0qWA=', $signature->getSignature($uri, ['pee' => 'haa']));
    }

    /**
     * @covers OAuth\OAuth1\Signature\Signature::__construct
     * @covers OAuth\OAuth1\Signature\Signature::setHashingAlgorithm
     * @covers OAuth\OAuth1\Signature\Signature::setTokenSecret
     * @covers OAuth\OAuth1\Signature\Signature::getSignature
     * @covers OAuth\OAuth1\Signature\Signature::buildSignatureDataString
     * @covers OAuth\OAuth1\Signature\Signature::hash
     * @covers OAuth\OAuth1\Signature\Signature::getSigningKey
     */
    public function testGetSignatureWithBarePathNonExplicitTrailingHostSlash()
    {
        $credentials = $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface');
        $credentials->expects($this->any())
            ->method('getConsumerSecret')
            ->will($this->returnValue('foo'));

        $signature = new Signature($credentials);

        $signature->setHashingAlgorithm('HMAC-SHA1');
        $signature->setTokenSecret('foo');

        $uri = new Url('http://peehaa:pass@nohost?param1=value1');
        $this->assertSame('evVuRC7y4Ozstd7S4ysBzJ2iymk=', $signature->getSignature($uri, ['pee' => 'haa']));
    }

    /**
     * @covers OAuth\OAuth1\Signature\Signature::__construct
     * @covers OAuth\OAuth1\Signature\Signature::setHashingAlgorithm
     * @covers OAuth\OAuth1\Signature\Signature::setTokenSecret
     * @covers OAuth\OAuth1\Signature\Signature::getSignature
     * @covers OAuth\OAuth1\Signature\Signature::buildSignatureDataString
     * @covers OAuth\OAuth1\Signature\Signature::hash
     * @covers OAuth\OAuth1\Signature\Signature::getSigningKey
     */
    public function testGetSignatureWithBarePathWithExplicitTrailingHostSlash()
    {
        $credentials = $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface');
        $credentials->expects($this->any())
            ->method('getConsumerSecret')
            ->will($this->returnValue('foo'));


        $signature = new Signature($credentials);

        $signature->setHashingAlgorithm('HMAC-SHA1');
        $signature->setTokenSecret('foo');

        $uri = new Url('http://peehaa:pass@nohost/?param1=value1');
        $this->assertSame('evVuRC7y4Ozstd7S4ysBzJ2iymk=', $signature->getSignature($uri, ['pee' => 'haa']));
    }

    /**
     * @covers OAuth\OAuth1\Signature\Signature::__construct
     * @covers OAuth\OAuth1\Signature\Signature::setHashingAlgorithm
     * @covers OAuth\OAuth1\Signature\Signature::setTokenSecret
     * @covers OAuth\OAuth1\Signature\Signature::getSignature
     * @covers OAuth\OAuth1\Signature\Signature::buildSignatureDataString
     * @covers OAuth\OAuth1\Signature\Signature::hash
     * @covers OAuth\OAuth1\Signature\Signature::getSigningKey
     */
    public function testGetSignatureNoTokenSecretSet()
    {
        $credentials = $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface');
        $credentials->expects($this->any())
            ->method('getConsumerSecret')
            ->will($this->returnValue('foo'));


        $signature = new Signature($credentials);

        $signature->setHashingAlgorithm('HMAC-SHA1');

        $uri = new Url('http://peehaa:pass@nohost/?param1=value1');
        $this->assertSame('0jaDjHF4StZz0+vkygICC6mToHs=', $signature->getSignature($uri, ['pee' => 'haa']));
    }

    /**
     * @covers OAuth\OAuth1\Signature\Signature::__construct
     * @covers OAuth\OAuth1\Signature\Signature::setHashingAlgorithm
     * @covers OAuth\OAuth1\Signature\Signature::setTokenSecret
     * @covers OAuth\OAuth1\Signature\Signature::getSignature
     * @covers OAuth\OAuth1\Signature\Signature::buildSignatureDataString
     * @covers OAuth\OAuth1\Signature\Signature::hash
     * @covers OAuth\OAuth1\Signature\Signature::getSigningKey
     */
    public function testGetSignatureThrowsExceptionOnUnsupportedAlgo()
    {
        $this->setExpectedException('\\OAuth\\OAuth1\\Signature\\Exception\\UnsupportedHashAlgorithmException');

        $credentials = $this->getMock('\\OAuth\\Common\\Consumer\\CredentialsInterface');
        $credentials->expects($this->any())
            ->method('getConsumerSecret')
            ->will($this->returnValue('foo'));


        $signature = new Signature($credentials);

        $signature->setHashingAlgorithm('UnsupportedAlgo');

        $uri = new Url('http://peehaa:pass@nohost/?param1=value1');
        $signature->getSignature($uri, ['pee' => 'haa']);
    }
}
