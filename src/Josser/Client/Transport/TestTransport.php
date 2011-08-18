<?php

/*
 * This file is part of the Josser package.
 *
 * (C) Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Josser\Client\Transport;

use Josser\Client\Transport\TransportInterface;

/**
 * Test transport.
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
class TestTransport implements TransportInterface
{
    /**
     * Response body.
     *
     * @var string
     */
    private $body;

    /**
     * Constructor.
     *
     * @param string $body
     */
    public function __construct($body = null)
    {
        $this->body = $body;
    }
    
    /**
     * Sets expected response body.
     *
     * @param $body
     * @return \Josser\Client\Transport\NullTransport
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Pretends to send data to remote service. Returns assigned response body.
     *
     * @throws \Josser\Exception\TransportFailedException
     * @param mixed $data
     * @return string
     */
    function send($data)
    {
        return $this->body;
    }
}
