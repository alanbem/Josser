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
use Josser\Client;

/**
 * Test class for Josser\Client
 */
class ClientTest extends JosserTestCase
{
    /**
     * @param string $requestMethod
     * @param array $requestParameters
     * @param mixed $responseResult
     *
     * @dataProvider requestAndNotificationDataProvider
     */
    public function testRequest($requestMethod, $requestParameters, $responseResult)
    {
        /* @var $transport \Josser\Client\Transport\TransportInterface */
        $transport = $this->getMockBuilder(Client\Transport\TransportInterface::class)->getMockForAbstractClass();
        /* @var $protocol \Josser\Protocol\Protocol */
        $protocol  = $this->getMockBuilder(Client\Protocol\Protocol::class)->getMockForAbstractClass();
        /* @var $response \Josser\Client\Response\ResponseInterface */
        $response = $this->getMockBuilder(Client\Response\ResponseInterface::class)->getMockForAbstractClass();
        $response->expects($this->any())
                 ->method('getResult')
                 ->will($this->returnValue($responseResult));

        /* @var $client \Josser\Client */
        $client = $this->getMockBuilder(Client::class)->setConstructorArgs([$transport, $protocol])->setMethods(['call'])->getMock();
        $client->expects($this->once())
               ->method('call')
               ->with(
                   $this->isInstanceOf('Josser\Client\Request\RequestInterface'), // todo: assert requests' method and parameters
                   $this->equalTo($transport),
                   $this->equalTo($protocol)
               )
               ->will($this->returnValue($response));

        $result = $client->request($requestMethod, $requestParameters);

        $this->assertEquals($responseResult, $result);
    }

    /**
     * @param string $requestMethod
     * @param array $requestParameters
     * @param mixed $responseResult
     *
     * @dataProvider requestAndNotificationDataProvider
     */
    public function testNotify($requestMethod, $requestParameters, $responseResult)
    {
        /* @var $transport \Josser\Client\Transport\TransportInterface */
        $transport = $this->getMockBuilder(Client\Transport\TransportInterface::class)->getMockForAbstractClass();
        /* @var $protocol \Josser\Protocol\Protocol */
        $protocol  = $this->getMockBuilder(Client\Protocol\Protocol::class)->getMockForAbstractClass();
        /* @var $response \Josser\Client\Response\ResponseInterface */
        $response = $this->getMockBuilder(Client\Response\ResponseInterface::class)->getMockForAbstractClass();
        $response->expects($this->any())
                 ->method('getResult')
                 ->will($this->returnValue($responseResult));

        /* @var $client \Josser\Client */
        $client = $this->getMockBuilder(Client::class)->setConstructorArgs([$transport, $protocol])->setMethods(['call'])->getMock();
        $client->expects($this->once())
               ->method('call')
               ->with(
                   $this->isInstanceOf('Josser\Client\Request\RequestInterface'), // todo: assert requests' method and parameters
                   $this->equalTo($transport),
                   $this->equalTo($protocol)
               )
               ->will($this->returnValue($response));

        $result = $client->notify($requestMethod, $requestParameters);

        $this->assertNull($result);
    }

    /**
     * Fixtures.
     *
     * @return array
     */
    public function requestAndNotificationDataProvider()
    {
        return array(
            array('math.sum', array(1,3), 4),
            array('user.name', array('id' => 100), 'jakob'),
            array('version', array(), '1.0.0.'),
            array('divide', array(3, 2), 1.5),
        );
    }
}
