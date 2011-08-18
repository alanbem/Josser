<?php

/*
 * This file is part of the Josser package.
 *
 * (C) Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Josser;

use Josser\Client\Request\RequestInterface;
use Josser\Client\Response\ResponseInterface;
use Josser\Client\Protocol\ProtocolInterface;
use Josser\Client\Response\NoResponse;
use Josser\Protocol\JsonRpc2;
use Josser\Client\Transport\TransportInterface;
use Josser\Exception\RequestResponseMismatchException;

/**
 * JSON-RPC client.
 *
 * Default protocol is JSON-RPC 2.0
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
class Client
{
    /**
     * JSON-RPC call transport.
     *
     * @var \Josser\Client\TransportInterface
     */
    private $transport;

    /**
     * JSON-RPC protocol
     *
     * @var \Josser\Client\ProtocolInterface
     */
    private $protocol;

    /**
     * Constructor.
     *
     * @param \Josser\Client\Transport\TransportInterface $transport
     * @param \Josser\Client\Protocol\ProtocolInterface|null $protocol
     */
    public function __construct(TransportInterface $transport, ProtocolInterface $protocol = null)
    {
        $this->transport = $transport;
        if(null === $protocol) {
            $protocol = new JsonRpc2;
        }
        $this->protocol = $protocol;
    }

    /**
     * Send request.
     *
     * @param $method
     * @param array $params
     * @return mixed
     */
    public function call($method, array $params)
    {
        return $this->request($method, $params);
    }

    /**
     * Alias of Client::call()
     *
     * @param $method
     * @param array $params
     * @return mixed
     */
    public function request($method, array $params = null)
    {
        if(null === $params) {
            $params = array();
        }
        $request = $this->protocol->createRequest($method, $params);
        $response = $this->send($request, $this->transport);
        return $response->getResult();
    }

    /**
     * Send notification request.
     *
     * @param string $method
     * @param array $params
     * @return void
     */
    public function notify($method, array $params)
    {
        $notification = $this->protocol->createNotification($method, $params);
        $this->send($notification, $this->transport);
    }

    /**
     * Execute JSON-RPC call.
     *
     * @throws \Josser\Exception\RequestResponseMismatchException
     * @param \Josser\Client\Request\RequestInterface $request
     * @param \Josser\Client\Transport\TransportInterface|null $transport
     * @return \Josser\Client\Response\ResponseInterface
     */
    public function send(RequestInterface $request, TransportInterface $transport = null)
    {
        if(null === $transport) { // swap transport easily
            $transport = $this->transport;
        }

        $this->protocol->validateRequest($request); // just in case
        $dto = $this->protocol->getRequestDataTransferObject($request);
        $requestJson = $this->protocol->getEndec()->encode($dto);
        $responseJson = $transport->send($requestJson);

        if($request->isNotification()) {
            return new NoResponse();
        }

        $responseDto = $this->protocol->getEndec()->decode($responseJson);
        $this->protocol->validateResponseDataTransferObject($responseDto); // just in case
        $response = $this->protocol->createResponse($responseDto);

        if(!$this->protocol->match($request, $response)) {
            throw new RequestResponseMismatchException($request, $response);
        }

        return $response;
    }
}
