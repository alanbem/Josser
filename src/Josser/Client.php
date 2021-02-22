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
use Symfony\Component\Serializer\Encoder\JsonEncoder;

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
        $response = $this->call($request, $this->transport, $this->protocol);
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
        $this->call($notification, $this->transport, $this->protocol);
    }

    /**
     * Execute JSON-RPC call.
     *
     * @throws \Josser\Exception\RequestResponseMismatchException
     * @param \Josser\Client\Request\RequestInterface $request
     * @param \Josser\Client\Transport\TransportInterface|null $transport
     * @param \Josser\Client\Protocol\Protocol|null $protocol
     * @return \Josser\Client\Response\ResponseInterface
     */
    public function call(RequestInterface $request, TransportInterface $transport = null, Protocol $protocol = null)
    {
        // allow transport switching
        if (null === $transport) {
            $transport = $this->transport;
        }

        // allow protocol switching
        if (null === $protocol) {
            $protocol = $this->protocol;
        }

        $requestDTO = $protocol->getRequestDataTransferObject($request);
        $encodedRequest = $protocol->getEncoder()->encode($requestDTO, JsonEncoder::FORMAT);
        $encodedResponse = $transport->send($encodedRequest);

        if($protocol->isNotification($request)) {
            return new NoResponse();
        }

        $responseDTO = $protocol->getDecoder()->decode($encodedResponse, JsonEncoder::FORMAT);
        $response = $protocol->createResponse($responseDTO);

        if(!$protocol->match($request, $response)) {
            throw new RequestResponseMismatchException($request, $response);
        }

        return $response;
    }
}
