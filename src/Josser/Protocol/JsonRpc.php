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

use Josser\Client\Protocol\ProtocolInterface;
use Josser\Client\Request\RequestInterface;
use Josser\Client\Request\Request;
use Josser\Client\Request\Notification;
use Josser\Client\Response\ResponseInterface;
use Josser\Client\Response;
use Josser\Endec\EndecInterface;

/**
 * JSON-RPC protocol base class.
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
abstract class JsonRpc implements ProtocolInterface
{
    /**
     * @var \Josser\Endec\EndecInterface
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
    public function generateRequestId()
    {
        return (string) uniqid();
    }

    /**
     * Retrieve Encoder/Decoder object.
     *
     * @return \Josser\Endec\JsonEndec
     */
    public function getEndec()
    {
        return $this->endec;
    }

    /**
     * Check whether $request is a notification.
     *
     * @param \Josser\Client\Request\RequestInterface $request
     * @return boolean
     */
    function isNotification(RequestInterface $request)
    {
        return $request->getId() === null;
    }
}
