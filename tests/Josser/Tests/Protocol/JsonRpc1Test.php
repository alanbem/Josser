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
use Josser\Protocol\JsonRpc1;
use Josser\Client\Request\Request;
use Josser\Client\Request\RequestInterface;
use Josser\Client\Request\Notification;
use Josser\Client\Response\Response;
use Josser\Exception\InvalidResponseException;

use Symfony\Component\Serializer\Encoder\JsonEncoder;

/**
 * Test class for Josser\Protocol\JsonRpc1.
 */
class JsonRpc1Test extends JosserTestCase
{
    /**
     * @var \Josser\Protocol\JsonRpc1
     */
    protected $protocol;

    public function setUp()
    {
        $this->protocol = new JsonRpc1;
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
        $protocol = new JsonRpc1($endec);

        $this->assertSame($endec, $protocol->getEncoder());
    }

    /**
     * @return void
     */
    public function testDecoderRetrieval()
    {
        $endec = new JsonEncoder;
        $protocol = new JsonRpc1($endec);

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
            array(array('result' => 'Hello JSON-RPC', 'error' => null, 'id' => 1)),
            array(array('result' => 'Hello JSON-RPC', 'error' => null, 'id' => null)), // notification
            array(array('result' => 'Hello JSON-RPC', 'error' => null, 'id' => "h312g48t3iuhr8")),
            array(array('result' => 43534, 'error' => null, 'id' => 1)),
            array(array('result' => 0.1, 'error' => null, 'id' => 1)),
            array(array('result' => array('Hello' => 'World'), 'error' => null, 'id' => 1)),
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
            array(array('result' => null, 'error' => array('message' => 'Error message', 'code' => 1000), 'id' => 1)), // RPC error
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
            array(array('result' => 'Hello JSON-RPC', 'error' => null)), // id missing
            array(array('result' => 'Hello JSON-RPC','id' => 1)), // error missing
            array(array('error' => null, 'id' => 1)), // result missing
            array(array('result' => null, 'error' => null, 'id' => 1)), // result is null & error is null
            array(array('result' => null, 'error' => array('code' => 1000), 'id' => 1)), // error message missing
            array(array('result' => null, 'error' => array('message' => 'Error message'), 'id' => 1)), // error code missing
            array(array('result' => null, 'error' => array('message' => 'Error message', 'code' => "iashdausgd"), 'id' => 1)), // code is not an integer
            array(array('result' => null, 'error' => array('message' => 'Error message', 'code' => array('error' => 'code')), 'id' => 1)), // code is not an integer
            array(array('result' => null, 'error' => array('message' => 324234, 'code' => 1000), 'id' => 1)), // error message is not a string
            array(array('result' => null, 'error' => array('message' => array('error' => 'message'), 'code' => 1000), 'id' => 1)), // error message is not a string
            array(array('result' => null, 'error' => 345, 'id' => 1)), // error is not an array
            array(array('result' => null, 'error' => "asdasr245", 'id' => 1)), // error is not an array
            array(array('result' => null, 'error' => array("error"), 'id' => 1)), // error is not an array
            array(array('result' => null, 'error' => array("error"), 'id' => new \stdClass)), // id is not int, string or null
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
            array(new Request('math.sum', array(1,2), 123324234), array('method' => 'math.sum', 'params' => array(1,2), 'id' => 123324234)),
            array(new Request('math.sum', array(1, 2), ''), array('method' => 'math.sum', 'params' => array(1,2), 'id' => '')), // invalid id
            array(new Request('system.exit', array(), null), array('method' => 'system.exit', 'params' => array(), 'id' => null)),
            array(new Notification('system.exit', array()), array('method' => 'system.exit', 'params' => array(), 'id' => null)),
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
            //array(new Request('', array(), 123324234)), // todo: check if empty string is valid method
            array(new Request(null, array(), 123324234)), // invalid method
            array(new Request(new \stdClass, array(), 123324234)), // invalid method
            // invalid id
            array(new Request('math.sum', array(1, 2), new \stdClass)), // invalid id, objects are not yet supported
            // invalid params
            array(new Request('math.sum', array('foo' => 'bar'), 123324234)), // invalid params
            array(new Request('math.sum', array('foo' => 'bar', 'bar' => 'foo'), 123324234)), // invalid params
            array(new Request('system.exit', array('foo' => 'bar'), null)), // notification with invalid params
            array(new Request('system.exit', array('foo' => 'bar', 'bar' => 'foo'), null)), // notification with invalid params
            array(new Notification('system.exit', array('foo' => 'bar'))), // notification with invalid params
            array(new Notification('system.exit', array('foo' => 'bar', 'bar' => 'foo'))), // notification with invalid params
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