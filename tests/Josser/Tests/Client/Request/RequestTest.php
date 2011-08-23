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
     * @param boolean $isNotification
     * @return void
     *
     * @dataProvider requestDataProvider
     * @covers \Josser\Client\Request\Request::getMethod
     * @covers \Josser\Client\Request\Request::getParams
     * @covers \Josser\Client\Request\Request::getId
     * @covers \Josser\Client\Request\Request::isNotification
     */
    public function testRequest($method, array $params, $id, $isNotification)
    {
        $request = new Request($method, $params, $id);

        $this->assertEquals($method, $request->getMethod());
        $this->assertEquals($params, $request->getParams());
        $this->assertEquals($id, $request->getId());
        $this->assertEquals($isNotification, $request->isNotification());
    }

    /**
     * Fixtures
     *
     * @return array
     */
    public function requestDataProvider()
    {
        return array(
            array('add', array(2, 2), 1, false),
            array('echo', array('Hello world'), 2, false),
            array('concat', array('Hello', ' ',  'world'), 3, false),
            array('getnews', array(), 4, false),
            array('add', array(2, 2), 'b286r', false),
            array('echo', array('Hello world'), 'uiashd873', false),
            array('concat', array('Hello', ' ',  'world'), 'n8923rra', false),
            array('getnews', array(), 'ygnqnor', false),

            array('logout', array(), null, true),
            array('punch', array('user123'), null, true),
        );
    }
}