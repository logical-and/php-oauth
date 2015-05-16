<?php

namespace OAuth\Unit\UserData\Utils;

use OAuth\UserData\Utils\ArrayUtils;

class ArrayUtilsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers Oauth\UserData\Utils\ArrayUtils::objectToArray
     */
    public function testObjectToArray()
    {
        $object = json_decode(
            '{
                "foo" : "bar",
                "nested" : {
                    "foo" : "bar"
                },
                "arr" : [1, 2, 3]
            }'
        );

        $expected = [
            'foo'    => 'bar',
            'nested' => [
                'foo' => 'bar'
            ],
            'arr'    => [1, 2, 3]
        ];

        $actual = ArrayUtils::objectToArray($object);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Oauth\UserData\Utils\ArrayUtils::removeKeys
     */
    public function testRemoveKeys()
    {
        $array = [
            'foo' => 1,
            'bar' => 2,
            'baz' => 3,
            'doo' => 4
        ];

        $keys = ['foo', 'doo'];

        $expected = [
            'bar' => 2,
            'baz' => 3
        ];

        $actual = ArrayUtils::removeKeys($array, $keys);

        $this->assertEquals($expected, $actual);
    }
}
