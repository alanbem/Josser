<?php

/*
 * This file is part of the Josser package.
 *
 * (C) Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Josser\Client\Request;

use Josser\Client\Protocol\ProtocolInterface;

/**
 * JSON-RPC request interface.
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
interface RequestInterface
{
    /**
     * @abstract
     * @return string
     */
    public function getMethod();

    /**
     * @abstract
     * @return array
     */
    public function getParams();
    
    /**
     * Get request id.
     *
     * @abstract
     * @throws \Josser\Exception\BadMethodCallException
     * @return null|number|string
     */
    public function getId();

    /**
     * Check whether remote call is a notification.
     *
     * @abstract
     * @return bool
     */
    public function isNotification();

    /**
     * Return DTO of a request.
     *
     * @abstract
     * @param \Josser\Client\Protocol\ProtocolInterface $protocol
     * @return mixed
     */
    public function getDataTransferObject(ProtocolInterface $protocol);
}
