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
use Josser\Protocol\JsonRpc2;
use Josser\Client\Request\Request;
use Josser\Client\Request\RequestInterface;
use Josser\Client\Request\Notification;
use Josser\Client\Response\Response;
use Josser\Exception\InvalidResponseException;

use Symfony\Component\Serializer\Encoder\JsonEncoder;

/**
 * Test class for Josser\Protocol\JsonRpc1.
 */
class JsonRpc2Test extends JosserTestCase
{
    /**
     * @var \Josser\Protocol\JsonRpc2
     */
    protected $protocol;

    public function setUp()
    {
        $this->protocol = new JsonRpc2;
    }

    /**
     * Default encoder for JsonRpc1 protocol is JsonEncoder.
     *
     * @return void
     *
     */
    public function testIfDefaultEncoderIsJsonEncoder()
    {
        $this->assertEquals(new JsonEncoder, $this->protocol->getEncoder());
    }

    /**
     * Default decoder for JsonRpc1 protocol is JsonEncoder.
     *
     * @return void
     */
    public function testIfDefaultDecoderIsJsonEncoder()
    {
        $this->assertEquals(new JsonEncoder, $this->protocol->getDecoder());
    }

    /**
     * @return void
     */
    public function testEncoderRetrieval()
    {
        $endec = new JsonEncoder;
        $protocol = new JsonRpc2($endec);

        $this->assertSame($endec, $protocol->getEncoder());
    }

    /**
     * @return void
     */
    public function testDecoderRetrieval()
    {
        $endec = new JsonEncoder;
        $protocol = new JsonRpc2($endec);

        $this->assertSame($endec, $protocol->getDecoder());
    }

    /**
     * Test protocols' response objects factory and its RPC fault detection if invalid DTO is provided.
     *
     * @param mixed $responseDataTransferObject
     * @return void
     *
     * @dataProvider validResponseDTOsWithoutRpcErrorDataProvider
     */
    public function testCreatingResponseFromValidDtoWithoutRpcError($responseDataTransferObject)
    {
        $response = $this->protocol->createResponse($responseDataTransferObject);
        $this->assertInstanceOf('Josser\Client\Response\ResponseInterface', $response);
    }

    /**
     * Test protocols' response objects factory and its RPC fault detection if invalid DTO is provided.
     *
     * @param mixed $responseDataTransferObject
     * @return void
     *
     * @dataProvider validResponseDTOsWithRpcErrorDataProvider
     */
    public function testCreatingResponseFromValidDtoWithRpcError($responseDataTransferObject)
    {
        $this->setExpectedException('Josser\Exception\RpcFaultException');

        $this->protocol->createResponse($responseDataTransferObject);
    }

    /**
     * Test protocols' response objects factory if invalid DTO is provided.
     *
     * @param mixed $responseDataTransferObject
     * @return void
     *
     * @dataProvider invalidResponseDTOsDataProvider
     */
    public function testCreatingResponseFromInvalidDTOs($responseDataTransferObject)
    {
        $this->setExpectedException('Josser\Exception\InvalidResponseException');

        $this->protocol->createResponse($responseDataTransferObject);
    }

    /**
     * Test protocols' request DTO factory if valid request is provided.
     *
     * @param \Josser\Client\Request\RequestInterface $request
     * @param array $expectedDataTransferObject
     * @return void
     *
     * @dataProvider validRequestsDataProvider
     */
    public function testCreatingDTOFromValidRequest(RequestInterface $request, $expectedDataTransferObject)
    {
        $dataTransferObject = $this->protocol->getRequestDataTransferObject($request);

        $this->assertEquals($expectedDataTransferObject, $dataTransferObject);
    }

    /**
     * Test protocols' request DTO factory if invalid request is provided.
     *
     * @param \Josser\Client\Request\RequestInterface $request
     * @return void
     *
     * @dataProvider invalidRequestsDataProvider
     */
    public function testCreatingDTOFromInvalidRequest(RequestInterface $request)
    {
        $this->setExpectedException('Josser\Exception\InvalidRequestException');

        $this->protocol->getRequestDataTransferObject($request);
    }

    /**
     * @param mixed $requestId
     * @param mixed $responseId
     * @param boolean $isMatch
     * @return void
     *
     * @dataProvider requestResponseMatchingDataProvider
     */
    public function testRequestResponseMatching($requestId, $responseId, $isMatch)
    {
        /* @var $requestStub \Josser\Client\Request\RequestInterface */
        $requestStub = $this->getMockBuilder('Josser\Client\Request\Request')
                            ->disableOriginalConstructor()
                            ->setMethods(array('getId'))
                            ->getMock();
        /* @var $responseStub \Josser\Client\Response\ResponseInterface */
        $requestStub->expects($this->once())
                    ->method('getId')
                    ->will($this->returnValue($requestId));

        $responseStub = $this->getMockBuilder('Josser\Client\Response\Response')
                             ->disableOriginalConstructor()
                             ->setMethods(array('getId'))
                             ->getMock();
        $responseStub->expects($this->once())
                     ->method('getId')
                     ->will($this->returnValue($responseId));


        $this->assertEquals($isMatch, $this->protocol->match($requestStub, $responseStub));
    }

    /**
     * @param \Josser\Client\Request\RequestInterface $request
     * @param boolean $isNotification
     *
     * @dataProvider requestsAndNotificationsDataProvider
     */
    public function testIsNotification(RequestInterface $request, $isNotification)
    {
        $this->assertEquals($isNotification, $this->protocol->isNotification($request));
    }

    /**
     * Test if protocol can generate unique request ids.
     */
    public function testGenerateRequestId()
    {
        $id = $this->protocol->generateRequestId();

        $this->assertNotNull($id);
    }

    /**
     * Fixtures
     *
     * @return array
     */
    public function validResponseDTOsWithoutRpcErrorDataProvider()
    {
        return array(
            array(array('jsonrpc' => '2.0', 'result' => 'Hello JSON-RPC', 'id' => 1)),
            array(array('jsonrpc' => '2.0', 'result' => 'Hello JSON-RPC', 'id' => "h312g48t3iuhr8")),
            array(array('jsonrpc' => '2.0', 'result' => 43534, 'id' => 1)),
            array(array('jsonrpc' => '2.0', 'result' => 0.1, 'id' => 1)),
            array(array('jsonrpc' => '2.0', 'result' => array('Hello' => 'World'), 'id' => 1)),
        );
    }

    /**
     * Fixtures
     *
     * @return array
     */
    public function validResponseDTOsWithRpcErrorDataProvider()
    {
        return array(
            array(array('jsonrpc' => '2.0', 'error' => array('message' => 'Error message', 'code' => 1000), 'id' => 1)), // RPC error
        );
    }

    /**
     * Fixtures.
     *
     * @return array
     */
    public function invalidResponseDTOsDataProvider()
    {
        return array(
            array(array('jsonrpc' => '2.0', 'result' => 'Hello JSON-RPC', 'error' => null)), // id missing
            array(array('jsonrpc' => '2.0', 'result' => 'Hello JSON-RPC', 'id' => null)), // wrong id
            array(array('result' => 'Hello JSON-RPC','id' => 1)), // jsonrpc missing
            array(array('jsonrpc' => new \stdClass(), 'error' => array('message' => 'Error message', 'code' => 1000),'id' => 1)), // jsonrpc missing
            array(array('jsonrpc' => null, 'error' => array('message' => 'Error message', 'code' => 1000),'id' => 1)), // jsonrpc is not '2.0'
            array(array('jsonrpc' => '', 'error' => array('message' => 'Error message', 'code' => 1000),'id' => 1)), // jsonrpc is not '2.0'
            array(array('jsonrpc' => array(), 'error' => array('message' => 'Error message', 'code' => 1000),'id' => 1)), // jsonrpc is not '2.0'
            array(array('jsonrpc' => '2.0', 'error' => null, 'id' => 1)), // result missing
            array(array('jsonrpc' => '2.0', 'id' => 1)), // result is null & error is null
            array(array('jsonrpc' => '2.0', 'result' => null, 'error' => null, 'id' => 1)), // result & error are missing
            array(array('jsonrpc' => '2.0', 'error' => array('code' => 1000), 'id' => 1)), // error message missing
            array(array('jsonrpc' => '2.0', 'error' => array('message' => 'Error message'), 'id' => 1)), // error code missing
            array(array('jsonrpc' => '2.0', 'error' => array('message' => 'Error message', 'code' => "iashdausgd"), 'id' => 1)), // code is not an integer
            array(array('jsonrpc' => '2.0', 'error' => array('message' => 'Error message', 'code' => array('error' => 'code')), 'id' => 1)), // code is not an integer
            array(array('jsonrpc' => '2.0', 'error' => array('message' => 324234, 'code' => 1000), 'id' => 1)), // error message is not a string
            array(array('jsonrpc' => '2.0', 'error' => array('message' => array('error' => 'message'), 'code' => 1000), 'id' => 1)), // error message is not a string
            array(array('jsonrpc' => '2.0', 'error' => 345, 'id' => 1)), // error is not an array
            array(array('jsonrpc' => '2.0', 'error' => "asdasr245", 'id' => 1)), // error is not an array
            array(array('jsonrpc' => '2.0', 'error' => array("error"), 'id' => 1)), // error is not an array
            array(array('jsonrpc' => '2.0', 'error' => array("error"), 'id' => new \stdClass)), // id is not int, string or null
            array(4), // response id not an array
            array('4dsf'), // response is not an array
            array(new \stdClass), // response is empty array/object
            array(array()), // response is empty array
        );
    }

    /**
     * Fixtures
     *
     * @return array
     */
    public function validRequestsDataProvider()
    {
        return array(
            array(new Request('math.sum', array(1,2), 123324234), array('jsonrpc' => '2.0', 'method' => 'math.sum', 'params' => array(1,2), 'id' => 123324234)),
            array(new Request('math.sum', array(1, 2), ''), array('jsonrpc' => '2.0', 'method' => 'math.sum', 'params' => array(1,2), 'id' => '')),
            array(new Request('math.divide', array('dividend' => 2, 'divisor' => 2), 123324234), array('jsonrpc' => '2.0', 'method' => 'math.divide', 'params' => array('dividend' => 2, 'divisor' => 2), 'id' => 123324234)),
            array(new Request('system.logout', array('message' => 'Bye bye'), null), array('jsonrpc' => '2.0', 'method' => 'system.logout', 'params' => array('message' => 'Bye bye'))),
            array(new Notification('system.logout', array('message' => 'Bye bye')), array('jsonrpc' => '2.0', 'method' => 'system.logout', 'params' => array('message' => 'Bye bye'))),
            array(new Request('system.logout', array('message' => 'Bye bye', 'send_to' => 'user123'), null), array('jsonrpc' => '2.0', 'method' => 'system.logout', 'params' => array('message' => 'Bye bye', 'send_to' => 'user123'))),
            array(new Notification('system.logout', array('message' => 'Bye bye', 'send_to' => 'user123')), array('jsonrpc' => '2.0', 'method' => 'system.logout', 'params' => array('message' => 'Bye bye', 'send_to' => 'user123'))),
        );
    }

    /**
     * Fixtures
     *
     * @return array
     */
    public function invalidRequestsDataProvider()
    {
        $mock = $this->getMock('Josser\Client\Request\RequestInterface');
        $mock->expects($this->atLeastOnce())
             ->method('getMethod')
             ->will($this->returnValue('mocked.math.sum'));
        $mock->expects($this->atLeastOnce())
             ->method('getId')
             ->will($this->returnValue(123324234));

        $request1 = clone $mock;
        $request1->expects($this->atLeastOnce())
                ->method('getParams')
                ->will($this->returnValue(''));

        $request2 = clone $mock;
        $request2->expects($this->atLeastOnce())
                ->method('getParams')
                ->will($this->returnValue(null));

        $request3 = clone $mock;
        $request3->expects($this->atLeastOnce())
                ->method('getParams')
                ->will($this->returnValue(new \stdClass));

        return array(
            // invalid methods
            array(new Request(null, array(), 123324234)), // invalid method
            array(new Request(new \stdClass, array(), 123324234)), // invalid method
            array(new Request(new \stdClass, array('foo', 'bar' => 'baz'), 123324234)), // invalid method
            // invalid id
            array(new Request('math.sum', array(1, 2), new \stdClass)), // invalid id, objects are not yet supported
            // invalid params
            array($request1),
            array($request2),
            array($request3),
        );
    }

    /**
     * Fixtures
     *
     * @return array
     */
    public function requestResponseMatchingDataProvider()
    {
        return array(
            array(1, 1, true),
            array('asd', 'asd', true),
            array(1, 'asd', false),
            array('asd', 1, false),
        );
    }

    /**
     * Fixtures
     *
     * @return array
     */
    public function requestsAndNotificationsDataProvider()
    {
        return array(
            array(new Request('math.sum', array(1,2), 123324234), false),
            array(new Request('system.exit', array(), null), true),
            array(new Notification('system.exit', array()), true),
        );
    }
}