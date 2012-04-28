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
use Josser\Client\Request\Notification;

/**
 * Test class for Josser\Client\Request\Request.
 */
class NotificationTest extends JosserTestCase
{
    /**
     * Test notification object.
     *
     * @param string $method
     * @param array $params
     * @return void
     *
     * @dataProvider notificationDataProvider
     */
    public function testNotification($method, array $params)
    {
        $notification = new Notification($method, $params);

        $this->assertEquals($method, $notification->getMethod());
        $this->assertEquals($params, $notification->getParams());
    }

    /**
     * Fixtures
     *
     * @return array
     */
    public function notificationDataProvider()
    {
        return array(
            array('add', array(2, 2)),
            array('echo', array('Hello world')),
            array('concat', array('Hello', ' ',  'world')),
            array('logout', array()),
        );
    }
}