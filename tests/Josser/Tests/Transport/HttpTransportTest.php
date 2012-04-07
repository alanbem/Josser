<?php

/*
 * This file is part of the Josser package.
 *
 * (C) Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Josser\Tests\Transport;

use Josser\Tests\TestCase as JosserTestCase;
use Josser\Client\Transport\HttpTransport;

/**
 * Test class for Josser\Transport\HttpTransport.
 */
class HttpTransportTest extends JosserTestCase
{
    /**
     * Test factory method of http transport.
     *
     * @param string $host
     * @param string $user
     * @param string $password
     * @param int $port
     * @param bool $isSecure
     * @param string $url
     *
     * @dataProvider connectionsProvider
     * @covers \Josser\Transport\HttpTransport::create
     */
    public function testFactory($host, $user, $password, $port, $isSecure, $url)
    {
        $transport1 = new HttpTransport($url);
        $transport2 = HttpTransport::create($host, $user, $password, $port, $isSecure);

        $this->assertEquals($transport1, $transport2);
    }

    /**
     * Fixtures
     *
     * @return array
     */
    public function connectionsProvider()
    {
        return array(
            array('localhost', 'user', 'password', 8332, true,  'https://user:password@localhost:8332'),
            array('localhost', 'user', 'password', 8332, false, 'http://user:password@localhost:8332'),
            array('localhost', 'user', 'password', 9000, true,  'https://user:password@localhost:9000'),
            array('localhost', 'user', 'password', 9000, false, 'http://user:password@localhost:9000'),
            array('127.0.0.1', 'user', 'password', 8332, true,  'https://user:password@127.0.0.1:8332'),
            array('127.0.0.1', 'user', 'password', 8332, false, 'http://user:password@127.0.0.1:8332'),
            array('127.0.0.1', 'user', 'password', 9000, true,  'https://user:password@127.0.0.1:9000'),
            array('127.0.0.1', 'user', 'password', 9000, false, 'http://user:password@127.0.0.1:9000'),
        );
    }
}