<?php

/*
 * This file is part of the Josser package.
 *
 * (C) Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Josser\Tests\Client\Request;

use Josser\Tests\TestCase as JosserTestCase;
use Josser\Client\Request\Request;

/**
 * Test class for Josser\Client\Request\Request.
 */
class RequestTest extends JosserTestCase
{
    /**
     * Test request object.
     *
     * @param string $method
     * @param array $params
     * @param mixed $id
     * @return void
     *
     * @dataProvider requestDataProvider
     */
    public function testRequest($method, array $params, $id)
    {
        $request = new Request($method, $params, $id);

        $this->assertEquals($method, $request->getMethod());
        $this->assertEquals($params, $request->getParams());
        $this->assertEquals($id, $request->getId());
    }

    /**
     * Fixtures
     *
     * @return array
     */
    public function requestDataProvider()
    {
        return array(
            array('add', array(2, 2), 1),
            array('echo', array('Hello world'), 2),
            array('concat', array('Hello', ' ',  'world'), 3),
            array('getnews', array(), 4),
            array('add', array(2, 2), 'b286r'),
            array('echo', array('Hello world'), 'uiashd873'),
            array('concat', array('Hello', ' ',  'world'), 'n8923rra'),
            array('getnews', array(), 'ygnqnor'),
        );
    }
}