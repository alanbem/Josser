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
use Josser\Client\Protocol\Protocol;
use Josser\Client\Response\NoResponse;
use Josser\Protocol\JsonRpc2;
use Josser\Client\Transport\TransportInterface;
use Josser\Exception\RequestResponseMismatchException;
use Josser\Client\Request\Request;
use Josser\Client\Request\Notification;

/**
 * JSON-RPC client.
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
class Client
{
    /**
     * JSON-RPC call transport.
     *
     * @var \Josser\Client\Transport\TransportInterface
     */
    private $transport;

    /**
     * JSON-RPC protocol
     *
     * @var \Josser\Client\Protocol\Protocol
     */
    private $protocol;

    /**
     * Constructor.
     *
     * @param \Josser\Client\Transport\TransportInterface $transport
     * @param \Josser\Client\Protocol\Protocol|null $protocol
     */
    public function __construct(TransportInterface $transport, Protocol $protocol)
    {
        $this->transport = $transport;
        $this->protocol = $protocol;
    }

    /**
     * Alias of Client::request()
     *
     * @param string $method
     * @param array $params
     * @param mixed|null $id
     * @return mixed
     */
    public function call($method, array $params = array(), $id = null)
    {
        return $this->request($method, $params, $id);
    }

    /**
     * Send request.
     *
     * @param string $method
     * @param array $params
     * @param mixed|null $id
     * @return mixed
     */
    public function request($method, array $params = array(), $id = null)
    {
        $request = new Request($method, $params, $id ?: $this->protocol->generateRequestId());
        $response = self::send($request, $this->transport, $this->protocol);
        return $response->getResult();
    }

    /**
     * Send notification request.
     *
     * @param string $method
     * @param array $params
     * @return void
     */
    public function notify($method, array $params = array())
    {
        $notification = new Notification($method, $params);
        self::send($notification, $this->transport, $this->protocol);
    }

    /**
     * Execute JSON-RPC call.
     *
     * @static
     * @throws \Josser\Exception\RequestResponseMismatchException
     * @param \Josser\Client\Request\RequestInterface $request
     * @param \Josser\Client\Transport\TransportInterface $transport
     * @param \Josser\Client\Protocol\Protocol $protocol
     * @return \Josser\Client\Response\ResponseInterface
     */
    public static function send(RequestInterface $request, TransportInterface $transport, Protocol $protocol)
    {
        $protocol->validateRequest($request);
        $requestDTO = $protocol->getRequestDataTransferObject($request);
        $encodedRequest = $protocol->getEncoder()->encode($requestDTO, null);
        $encodedResponse = $transport->send($encodedRequest);

        if($protocol->isNotification($request)) {
            return new NoResponse();
        }

        $responseDTO = $protocol->getDecoder()->decode($encodedResponse, null);
        $protocol->validateResponseDataTransferObject($responseDTO);
        $response = $protocol->createResponse($responseDTO);

        if(!$protocol->match($request, $response)) {
            throw new RequestResponseMismatchException($request, $response);
        }

        return $response;
    }
}
