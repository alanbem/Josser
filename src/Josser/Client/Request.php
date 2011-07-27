<?php

/*
 * This file is part of the Josser package.
 *
 * (C) Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Josser\Client;

use Josser\Exception\InvalidArgumentException;
use Josser\Exception\BadMethodCallException;
use Josser\Client\RequestInterface;
use Josser\Client\ProtocolInterface;

/**
 * JSON-RPC request object.
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
class Request implements RequestInterface
{
    /**
     * A string containing the name of the method to be invoked.
     *
     * @var string
     */
    private $method;

    /**
     * A Structured value that holds the parameter values to be used during the invocation of the method.
     *
     * @var array
     */
    private $params;

    /**
     * An identifier established by the client.
     *
     * @var mixed
     */
    private $id;

    /**
     * Constructor.
     *
     * @param string $method
     * @param array|null $params
     * @param mixed $id
     */
    public function __construct($method, array $params = null, $id = null)
    {
        $this->method = $method;
        $this->params = $params;
        $this->id     = $id;
    }

    /**
     * Get remote method name.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get method parameters.
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Get request id.
     *
     * @return null|number|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Check whether $this request is a notification.
     *
     * @return bool
     */
    public function isNotification()
    {
        return null === $this->id;
    }

    /**
     * Return DTO of $this request.
     *
     * @param \Josser\Client\ProtocolInterface $protocol
     * @return mixed
     */
    public function getDataTransferObject(ProtocolInterface $protocol)
    {
        return $protocol->getRequestDataTransferObject($this);
    }
}
