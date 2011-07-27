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

/**
 * JSON-RPC transport interface.
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
interface TransportInterface
{
    /**
     * Send data to remote JSON-RPC service.
     *
     * @throws \Josser\Exception\TransportFailedException
     * @param mixed $data
     * @return string
     */
    function send($data);
}
