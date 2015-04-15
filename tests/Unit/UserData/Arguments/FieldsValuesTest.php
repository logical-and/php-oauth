<?php

namespace OAuthTest\Unit\UserData\Arguments;

use OAuth\UserData\Arguments\FieldsValues;

class FieldsValuesTest extends \PHPUnit_Framework_TestCase {

	public function testConstructViaStaticMethodIsEqual()
	{
		$this->assertEquals(
			FieldsValues::construct(['field1'])->getSupportedFields(),
			(new FieldsValues(['field1']))->getSupportedFields()
		);
	}

	public function testSetFieldsIsWorksCorrectly()
	{
		$fieldsValues = FieldsValues::construct()->setFieldsWithValues([
			'field1',
			'field2' => 'value2',
			'field3'
		]);

		$this->assertEquals(['field1', 'field2', 'field3'], $fieldsValues->getSupportedFields());
		$this->assertEquals(['field2' => 'value2'], $fieldsValues->getFieldsValues());
	}

	/**
	 * @depends testSetFieldsIsWorksCorrectly
	 */
	public function testConstructWithFieldsArgument()
	{
		$fieldsValues = new FieldsValues([
			'field1',
			'field2' => 'value2',
			'field3'
		]);

		$this->assertEquals(['field1', 'field2', 'field3'], $fieldsValues->getSupportedFields());
		$this->assertEquals(['field2' => 'value2'], $fieldsValues->getFieldsValues());
	}

	/**
	 * @depends testConstructWithFieldsArgument
	 */
	public function testSetFieldValue()
	{
		$fieldsValues = new FieldsValues([
			'field1',
			'field2' => 'value2',
			'field3'
		]);

		// Change value
		$fieldsValues->fieldValue('field2', '2value');
		$this->assertEquals(['field2' => '2value'], $fieldsValues->getFieldsValues());

		// Set value, when field was empty
		$fieldsValues->fieldValue('field3', 'value3');
		$this->assertEquals(['field2' => '2value', 'field3' => 'value3'], $fieldsValues->getFieldsValues());

		// Add new field
		$fieldsValues->fieldValue('field4', 'value4');
		$this->assertEquals(['field2' => '2value', 'field3' => 'value3', 'field4' => 'value4'], $fieldsValues->getFieldsValues());
	}
}
