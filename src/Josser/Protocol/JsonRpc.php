<?php

/*
 * This file is part of the Josser package.
 *
 * (C) Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Josser\Protocol;

use Josser\Client\Protocol\ProtocolInterface as ClientProtocol;
use Josser\Client\Request\RequestInterface;
use Josser\Client\Request\Request;
use Josser\Client\Response\ResponseInterface;
use Josser\Client\Response;
use Josser\Endec\EndecInterface;

/**
 * JSON-RPC protocol base class.
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
abstract class JsonRpc implements ClientProtocol
{
    /**
     * @var EndecInterface
     */
    protected $endec;

    /**
     * Constructor.
     *
     * @param \Josser\Endec\EndecInterface $endec
     */
    public function __construct(EndecInterface $endec)
    {
        $this->endec = $endec;
    }

    /**
     * Create notification object.
     *
     * @param string $method
     * @param array $params
     * @return \Josser\Client\Request\RequestInterface
     */
    public function createNotification($method, array $params = null)
    {
        $notification = new Request($method, $params);
        $this->validateRequest($notification);
        return $notification;
    }

    /**
     * Create request object.
     *
     * @param string $method
     * @param array $params
     * @return \Josser\Client\Request\RequestInterface
     */
    final public function createRequest($method, array $params = null)
    {
        $request = new Request($method, $params, $this->generateRequestId());
        $this->validateRequest($request);
        return $request;
    }

    /**
     * Checks whether request matches response.
     *
     * @param \Josser\Client\Request\RequestInterface $request
     * @param \Josser\Client\Response\ResponseInterface $response
     * @return boolean
     */
    function match(RequestInterface $request, ResponseInterface $response)
    {
        return (boolean) ($request->getId() == $response->getId());
    }

    /**
     * Check whether $array is indexed array.
     *
     * @param array $array
     * @return bool
     */
    protected function isIndexed(array $array)
    {
        foreach ($array as $key => $value) {
            if (!is_numeric($key)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check whether $array is associative array.
     *
     * @param array $array
     * @return bool
     */
    protected function isAssociative(array $array)
    {
        foreach ($array as $key => $value) {
            if (!is_string($key)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Generate random string for a response identifier.
     *
     * @return string
     */
    protected  function generateRequestId()
    {
        return (string) uniqid();
    }

    /**
     * @return \Josser\Endec\JsonEndec
     */
    public function getEndec()
    {
        return $this->endec;
    }
}
