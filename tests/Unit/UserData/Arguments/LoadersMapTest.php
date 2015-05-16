<?php

namespace OAuthTest\Unit\UserData\Arguments;

use OAuth\UserData\Arguments\LoadersMap;
use OAuth\UserData\Exception\GenericException;

class LoadersMapTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructViaStaticMethodIsEqual()
    {
        $this->assertEquals(
            LoadersMap::construct()->loader('test')->addField('field')->getLoaderForField('field'),
            (new LoadersMap())->loader('test')->addField('field')->getLoaderForField('field')
        );
    }

    /**
     * @expectedException \OAuth\UserData\Exception\GenericException
     */
    public function testExceptionThrownIfNoContextDefined()
    {
        LoadersMap::construct()->getLoaderForField('someField');
    }

    public function testSet()
    {
        $loadersMap = new LoadersMap();
        $loadersMap->set(
            [
                'loader'        => ['field1', 'field2'],
                'anotherLoader' => ['field3']
            ]
        );

        $this->assertEquals('loader', $loadersMap->getLoaderForField('field1'));
        $this->assertEquals('loader', $loadersMap->getLoaderForField('field2'));
        $this->assertEquals('anotherLoader', $loadersMap->getLoaderForField('field3'));
    }

    public function testConstructWithSetArguments()
    {
        $this->assertEquals('loader', LoadersMap::construct(['loader' => ['field']])->getLoaderForField('field'));
    }

    public function testExceptionThrownIfNoLoaderDefined()
    {
        $exceptionClass = '\\OAuth\\UserData\\Exception\\GenericException';
        $loadersMap = new LoadersMap();

        // addField
        try {
            $loadersMap->addField('field');
            throw new \RuntimeException('Will be throw in any case');
        } catch (GenericException $e) {
            $this->assertInstanceOf($exceptionClass, $e);
        }

        // setFields
        try {
            $loadersMap->setFields(['field']);
            throw new \RuntimeException('Will be throw in any case');
        } catch (GenericException $e) {
            $this->assertInstanceOf($exceptionClass, $e);
        }

        // removeField
        try {
            $loadersMap->removeField('field');
            throw new \RuntimeException('Will be throw in any case');
        } catch (GenericException $e) {
            $this->assertInstanceOf($exceptionClass, $e);
        }

        // readdField
        try {
            $loadersMap->readdField('field');
            throw new \RuntimeException('Will be throw in any case');
        } catch (GenericException $e) {
            $this->assertInstanceOf($exceptionClass, $e);
        }
    }

    public function testAddField()
    {
        $loadersMap = new LoadersMap();

        $loadersMap->loader('customLoader')->addField('field1');

        $this->assertEquals('customLoader', $loadersMap->getLoaderForField('field1'));
    }

    public function testAddFields()
    {
        $loadersMap = new LoadersMap();

        $loadersMap->loader('customLoader')->addFields(['field1', 'field2']);

        $this->assertEquals('customLoader', $loadersMap->getLoaderForField('field1'));
        $this->assertEquals('customLoader', $loadersMap->getLoaderForField('field2'));
    }

    public function testSetFields()
    {
        $loadersMap = new LoadersMap();

        $loadersMap->loader('customLoader')->addField('field1');
        $loadersMap->setFields(['field2']);

        // Can be found
        $this->assertEquals('customLoader', $loadersMap->getLoaderForField('field2'));

        // Cannot be found
        $this->setExpectedException('\\OAuth\\UserData\\Exception\\GenericException');
        $this->assertEquals('customLoader', $loadersMap->getLoaderForField('field1'));
    }

    /**
     * @expectedException \OAuth\UserData\Exception\GenericException
     */
    public function testRemoveField()
    {
        $loadersMap = new LoadersMap();

        $loadersMap->loader('customLoader')->addField('field1');
        $this->assertEquals('customLoader', $loadersMap->getLoaderForField('field1'));

        $loadersMap->removeField('field1');
        $this->assertEquals('customLoader', $loadersMap->getLoaderForField('field1'));
    }

    /**
     * @expectedException \OAuth\UserData\Exception\GenericException
     */
    public function testRemoveFields()
    {
        $loadersMap = new LoadersMap();

        $loadersMap->loader('customLoader')->addField('field1');
        $this->assertEquals('customLoader', $loadersMap->getLoaderForField('field1'));

        $loadersMap->removeFields(['field1']);
        $this->assertEquals('customLoader', $loadersMap->getLoaderForField('field1'));
    }

    public function testReAddField()
    {
        $loadersMap = new LoadersMap();

        $loadersMap->loader('customLoader')->addField('field1');
        $this->assertEquals('customLoader', $loadersMap->getLoaderForField('field1'));

        $loadersMap->loader('customLoader2')->readdField('field1');
        $this->assertEquals('customLoader2', $loadersMap->getLoaderForField('field1'));
    }
}
