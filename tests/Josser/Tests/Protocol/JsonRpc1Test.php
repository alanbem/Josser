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
use Josser\Endec\JsonEndec;
use Josser\Client\Request\Request;
use Josser\Client\Response\Response;
use Josser\Exception\InvalidResponseException;

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
     * Default encoder/decoder for JsonRpc1 protocol is JsonEndec.
     *
     * @return void
     *
     * @covers JsonEndec::getEndec
     */
    public function testIfDefaultEncoderIsJsonEncoder()
    {
        $this->assertEquals(new JsonEndec, $this->protocol->getEndec());
    }

    /**
     * @return void
     *
     * @covers JsonEndec::getEndec
     */
    public function testEndecRetrieval()
    {
        $endec = new JsonEndec;
        $protocol = new JsonRpc1($endec);

        $this->assertSame($endec, $protocol->getEndec());
    }

    /**
     * @param mixed $responseDataTransferObject
     * @param boolean $isValid
     * @return void
     *
     * @dataProvider responseDataProvider
     * @covers Josser\Protocol\JsonRpc1::validateResponseDataTransferObject
     */
    public function testResponseValidation($responseDataTransferObject, $isValid)
    {
        if(false == $isValid) {
            $this->setExpectedException("Josser\Exception\InvalidResponseException");
        }

        $this->protocol->validateResponseDataTransferObject($responseDataTransferObject);
    }

    /**
     * Test protocols' response objects factory and its RPC fault detection.
     *
     * @param mixed $responseDataTransferObject
     * @return void
     *
     * @dataProvider validResponseDataProvider
     * @covers Josser\Protocol\JsonRpc1::createResponse
     */
    public function testResponseFactory($responseDataTransferObject)
    {
        if(null !== $responseDataTransferObject['error']) { // response is valid, but rpc error has been sent
            $this->setExpectedException("Josser\Exception\RpcFaultException");
        }

        $response = $this->protocol->createResponse($responseDataTransferObject);

        $this->assertInstanceOf('Josser\Client\Response\ResponseInterface', $response);
    }

    /**
     * @param mixed $requestId
     * @param mixed $responseId
     * @param boolean $isMatch
     * @return void
     *
     * @dataProvider requestResponseMatchingDataProvider
     * @covers Josser\Protocol\JsonRpc1::match
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
     * Fixtures
     *
     * @return array
     */
    protected function getValidResponseDTOs()
    {
        return array(
            array('result' => 'Hello JSON-RPC', 'error' => null, 'id' => 1),
            array('result' => null, 'error' => array('message' => 'Error message', 'code' => 1000), 'id' => 1), // RPC error
            array('result' => 'Hello JSON-RPC', 'error' => null, 'id' => null), // notification
            array('result' => 'Hello JSON-RPC', 'error' => null, 'id' => "h312g48t3iuhr8"),
            array('result' => 43534, 'error' => null, 'id' => 1),
            array('result' => 0.1, 'error' => null, 'id' => 1),
            array('result' => array('Hello' => 'World'), 'error' => null, 'id' => 1),
        );
    }

    /**
     * Fixtures.
     *
     * @return array
     */
    protected function getInvalidResponseDTOs()
    {
        return array(
            array('result' => 'Hello JSON-RPC', 'error' => null), // id missing
            array('result' => 'Hello JSON-RPC','id' => 1), // error missing
            array('error' => null, 'id' => 1), // result missing
            array('result' => null, 'error' => null, 'id' => 1), // result is null & error is null
            array('result' => null, 'error' => array('code' => 1000), 'id' => 1), // error message missing
            array('result' => null, 'error' => array('message' => 'Error message'), 'id' => 1), // error code missing
            array('result' => null, 'error' => array('message' => 'Error message', 'code' => "iashdausgd"), 'id' => 1), // code is not an integer
            array('result' => null, 'error' => array('message' => 'Error message', 'code' => array('error' => 'code')), 'id' => 1), // code is not an integer
            array('result' => null, 'error' => array('message' => 324234, 'code' => 1000), 'id' => 1), // error message is not a string
            array('result' => null, 'error' => array('message' => array('error' => 'message'), 'code' => 1000), 'id' => 1), // error message is not a string
            array('result' => null, 'error' => 345, 'id' => 1), // error is not an array
            array('result' => null, 'error' => "asdasr245", 'id' => 1), // error is not an array
            array('result' => null, 'error' => array("error"), 'id' => 1), // error is not an array
            array(4), // response id not an array
            array('4dsf'), // response is not an array
            array(new \stdClass), // response is empty array/object
            array(array()), // response is empty array
        );
    }

    /**
     * Test data.
     *
     * @return array
     */
    public function validResponseDataProvider()
    {
        $responses = array();
        foreach($this->getValidResponseDTOs() as $response) {
            $responses[] = array($response, true);
        }
        return $responses;
    }

    /**
     * Test data.
     *
     * @return array
     */
    public function invalidResponseDataProvider()
    {
        $responses = array();
        foreach($this->getInvalidResponseDTOs() as $response) {
            $responses[] = array($response, false);
        }
        return $responses;
    }

    public function responseDataProvider()
    {
        return array_merge($this->validResponseDataProvider(), $this->invalidResponseDataProvider());
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
}