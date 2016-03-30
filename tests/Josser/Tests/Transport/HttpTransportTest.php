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

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Josser\Tests\TestCase as JosserTestCase;
use Josser\Client\Transport\HttpTransport;

/**
 * Test class for Josser\Transport\HttpTransport.
 */
class HttpTransportTest extends JosserTestCase
{
    /**
     *
     * Test getters of transport object.
     */
    public function testGetters()
    {
        $guzzle    = $this->getMock(Client::class);
        $transport = new HttpTransport($guzzle);

        $this->assertSame($guzzle, $transport->getGuzzle());
    }

    public function testSend()
    {
        $mock = new MockHandler([
            new Response(200, [], '1'),
        ]);

        $handler = HandlerStack::create($mock);
        $guzzle = new Client(['handler' => $handler]);

        $transport = new HttpTransport($guzzle);

        $result = $transport->send('[]');

        $this->assertEquals('1', $result);
    }

    /**
     * Test transport if there is no connection.
     */
    public function testNoConnection()
    {
        $mock = new MockHandler([
            new RequestException("Error Communicating with Server", new Request('GET', 'test'))
        ]);

        $handler = HandlerStack::create($mock);
        $guzzle = new Client(['handler' => $handler]);

        $transport = new HttpTransport($guzzle);

        $this->setExpectedException('Josser\Exception\TransportFailureException');

        $transport->send('[]');
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
