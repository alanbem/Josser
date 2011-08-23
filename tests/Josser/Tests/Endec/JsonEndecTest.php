<?php

/*
 * This file is part of the Josser package.
 *
 * (C) Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Josser\Tests;

use Josser\Tests\TestCase as JosserTestCase;
use Josser\Endec\JsonEndec;

/**
 * Test class for Josser\Endec\JsonEndec.
 */
class JsonEndecTest extends JosserTestCase
{
    /**
     * @var \Josser\Endec\JsonEndec
     */
    private $encoder;

    public function setUp()
    {
        $this->encoder = new JsonEndec;
    }

    /**
     * @param mixed $data
     * @param string $expected
     * @return void
     *
     * @dataProvider encodeDataProvider
     * @covers \Josser\Endec\JsonEndec::encode
     */
    public function testEncode($data, $expected)
    {
        $this->assertEquals($expected, $this->encoder->encode($data));
    }

    /**
     * @param string $data
     * @param mixed $expected
     * @return void
     *
     * @dataProvider decodeDataProvider
     * @covers \Josser\Endec\JsonEndec::decode
     */
    public function testDecode($data, $expected)
    {
        $this->assertEquals($expected, $this->encoder->decode($data));
    }

    /**
     * Test data for JsonEndec::encode()
     *
     * @return array
     */
    public function encodeDataProvider()
    {
        return array(
            array(1, 1),
            array('test', '"test"'),
            array(array(1, 2), '[1,2]'),
            array(array(1, 'two'), '[1,"two"]'),
            array(array('one' => 1, 'two' => 'two'), '{"one":1,"two":"two"}'),
            array(array('one', 'two' => 2), '{"0":"one","two":2}'),
        );
    }

    /**
     * Test data for JsonEndec::decode()
     *
     * @return array
     */
    public function decodeDataProvider()
    {
        return array(
            array(1, 1),
            array('"test"', 'test'),
            array('[1,2]', array(1, 2)),
            array('[1,"two"]', array(1, 'two')),
            array('{"one":1,"two":"two"}', array('one' => 1, 'two' => 'two')),
            array('{"0":"one","two":2}', array('one', 'two' => 2)),
        );
    }
}