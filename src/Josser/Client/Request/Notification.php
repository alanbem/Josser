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

use Josser\Exception\InvalidArgumentException;
use Josser\Exception\BadMethodCallException;
use Josser\Client\Request\Request;
use Josser\Client\Protocol\ProtocolInterface;

/**
 * JSON-RPC explicit client notification object.
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
class Notification extends Request
{
    /**
     * Constructor.
     *
     * @param string $method
     * @param array|null $params
     */
    public function __construct($method, array $params = null)
    {
        parent::__construct($method, $params, null);
    }

    /**
     * Get request id.
     *
     * @return null|number|string
     */
    public function getId()
    {
        return null;
    }

    /**
     * Check whether $this request is a notification.
     *
     * @return bool
     */
    final public function isNotification()
    {
        return true;
    }
}
