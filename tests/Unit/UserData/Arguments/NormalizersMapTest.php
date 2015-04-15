<?php

namespace OAuthTest\Unit\UserData\Arguments;

use OAuth\UserData\Arguments\NormalizersMap;

class NormalizersMapTest extends \PHPUnit_Framework_TestCase {

	public function testConstructViaStaticMethodIsEqual()
	{
		$this->assertEquals(
			NormalizersMap::construct()->set(['field' => 'path'])->getNormalizerForField('field'),
			(new NormalizersMap())->set(['field' => 'path'])->getNormalizerForField('field')
		);
	}

	public function testConstructWithSetArguments()
	{
		$this->assertEquals(
			(new NormalizersMap(['field' => 'path']))->getNormalizerForField('field'),
			(new NormalizersMap())->set(['field' => 'path'])->getNormalizerForField('field')
		);
	}

	public function testAdd()
	{
		$normalizersMap = NormalizersMap::construct();

		$this->assertFalse($normalizersMap->getNormalizerForField('field1'));

		$normalizersMap->add([
			'field1' => 'fieldPath',
			'field2' => '::methodName',
			'field3' => ['field.path', 'defaultValue']
		]);

		$field1Data = [
			'type'               => NormalizersMap::TYPE_ARRAY_PATH,
			'path'               => 'fieldPath',
			'defaultValue'       => NULL,
			'contextPath'        => '',
			'pathWithoutContext' => 'fieldPath'
		];
		$field2Data = [
			'type'               => NormalizersMap::TYPE_METHOD,
			'method'               => 'methodName'
		];
		$field3Data = [
			'type'               => NormalizersMap::TYPE_ARRAY_PATH,
			'path'               => 'field.path',
			'defaultValue'       => 'defaultValue',
			'contextPath'        => '',
			'pathWithoutContext' => 'field.path'
		];

		$this->assertEquals($field1Data, $normalizersMap->getNormalizerForField('field1'));
		$this->assertEquals($field2Data, $normalizersMap->getNormalizerForField('field2'));
		$this->assertEquals($field3Data, $normalizersMap->getNormalizerForField('field3'));

		$this->assertEquals(['field1' => $field1Data, 'field3' => $field3Data], $normalizersMap->getPathNormalizers());
		$this->assertEquals(['field2' => $field2Data], $normalizersMap->getMethodNormalizers());
	}

	public function testSet()
	{
		$normalizersMap = NormalizersMap::construct([
			'field1' => 'fieldPath1',
			'field2' => 'fieldPath2',
		]);

		$this->assertEquals([
			'type'               => NormalizersMap::TYPE_ARRAY_PATH,
			'path'               => 'fieldPath1',
			'defaultValue'       => NULL,
			'contextPath'        => '',
			'pathWithoutContext' => 'fieldPath1'
		], $normalizersMap->getNormalizerForField('field1'));
		$this->assertEquals([
			'type'               => NormalizersMap::TYPE_ARRAY_PATH,
			'path'               => 'fieldPath2',
			'defaultValue'       => NULL,
			'contextPath'        => '',
			'pathWithoutContext' => 'fieldPath2'
		], $normalizersMap->getNormalizerForField('field2'));

		$normalizersMap->set([
			'field2' => 'fieldPath2',
		]);

		$this->assertFalse($normalizersMap->getNormalizerForField('field1'));
		$this->assertEquals([
			'type'               => NormalizersMap::TYPE_ARRAY_PATH,
			'path'               => 'fieldPath2',
			'defaultValue'       => NULL,
			'contextPath'        => '',
			'pathWithoutContext' => 'fieldPath2'
		], $normalizersMap->getNormalizerForField('field2'));
	}

	public function testMethod()
	{
		$normalizersMap = NormalizersMap::construct([
			'field' => '::methodName'
		])
			->method('field', 'methodName2');

		$this->assertEquals([
			'type'               => NormalizersMap::TYPE_METHOD,
			'method'               => 'methodName2'
		], $normalizersMap->getNormalizerForField('field'));
	}

	public function testMethods()
	{
		$normalizersMap = NormalizersMap::construct([
			'field' => '::methodName'
		])
			->methods([
				'field' => 'methodName1',
				'field2' => 'methodName2'
			]);

		$this->assertEquals([
			'type'               => NormalizersMap::TYPE_METHOD,
			'method'               => 'methodName1'
		], $normalizersMap->getNormalizerForField('field'));

		$this->assertEquals([
			'type'               => NormalizersMap::TYPE_METHOD,
			'method'               => 'methodName2'
		], $normalizersMap->getNormalizerForField('field2'));
	}

	public function testPath()
	{
		$normalizersMap = NormalizersMap::construct([
			'field' => ['path', 'defaultValue']
		])
			->path('field', 'path2', 'defaultValue2');

		$this->assertEquals([
			'type'               => NormalizersMap::TYPE_ARRAY_PATH,
			'path'               => 'path2',
			'defaultValue'       => 'defaultValue2',
			'contextPath'        => '',
			'pathWithoutContext' => 'path2'
		], $normalizersMap->getNormalizerForField('field'));
	}

	public function testPaths()
	{
		$normalizersMap = NormalizersMap::construct([
			'field' => 'path'
		])
			->paths([
				'field' => 'path1',
				'field2' => 'path2'
			]);

		$this->assertEquals([
			'type'               => NormalizersMap::TYPE_ARRAY_PATH,
			'path'               => 'path1',
			'defaultValue'       => NULL,
			'contextPath'        => '',
			'pathWithoutContext' => 'path1'
		], $normalizersMap->getNormalizerForField('field'));
		$this->assertEquals([
			'type'               => NormalizersMap::TYPE_ARRAY_PATH,
			'path'               => 'path2',
			'defaultValue'       => NULL,
			'contextPath'        => '',
			'pathWithoutContext' => 'path2'
		], $normalizersMap->getNormalizerForField('field2'));
	}

	public function testNoNormalizer()
	{
		$normalizersMap = NormalizersMap::construct([
			'field1' => 'fieldPath1'
		]);

		$this->assertEquals([
			'type'               => NormalizersMap::TYPE_ARRAY_PATH,
			'path'               => 'fieldPath1',
			'defaultValue'       => NULL,
			'contextPath'        => '',
			'pathWithoutContext' => 'fieldPath1'
		], $normalizersMap->getNormalizerForField('field1'));

		// Apply method right now
		$normalizersMap->noNormalizer('field1');

		$this->assertFalse($normalizersMap->getNormalizerForField('field1'));
	}

	public function testPrefilled()
	{
		$this->assertEquals([
			'type'  => NormalizersMap::TYPE_PREFILLED_VALUE,
			'value' => 'prefilledValue'
		], NormalizersMap::construct()->prefilled('field', 'prefilledValue')->getNormalizerForField('field'));
	}

	public function testPathContext()
	{
		$normalizersMap = NormalizersMap::construct()
			->path('field1', 'path')
			->pathContext('someContext')
			->path('field2', 'path');

		$this->assertEquals([
			'type'               => NormalizersMap::TYPE_ARRAY_PATH,
			'path'               => 'path',
			'defaultValue'       => NULL,
			'contextPath'        => '',
			'pathWithoutContext' => 'path'
		], $normalizersMap->getNormalizerForField('field1'));
		$this->assertEquals([
			'type'               => NormalizersMap::TYPE_ARRAY_PATH,
			'path'               => 'someContext.path',
			'defaultValue'       => NULL,
			'contextPath'        => 'someContext.',
			'pathWithoutContext' => 'path'
		], $normalizersMap->getNormalizerForField('field2'));
	}

	public function testPrependByPathContext()
	{
		$normalizersMap = NormalizersMap::construct()
			->path('field1', 'path')
			->prependByPathContext('someContext')
			->path('field2', 'path');

		$this->assertEquals([
			'type'               => NormalizersMap::TYPE_ARRAY_PATH,
			'path'               => 'someContext.path',
			'defaultValue'       => NULL,
			'contextPath'        => 'someContext.',
			'pathWithoutContext' => 'path'
		], $normalizersMap->getNormalizerForField('field1'));
		$this->assertEquals([
			'type'               => NormalizersMap::TYPE_ARRAY_PATH,
			'path'               => 'someContext.path',
			'defaultValue'       => NULL,
			'contextPath'        => 'someContext.',
			'pathWithoutContext' => 'path'
		], $normalizersMap->getNormalizerForField('field2'));
	}
}
