<?php

/*
 * This file is part of the Josser package.
 *
 * (C) Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Josser\Exception;

use Josser\Exception\JosserException;

/**
 * RPC fault exception.
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
class RpcFaultException extends \RuntimeException implements JosserException
{
    public function __construct($message, $code = null, $previous = null)
    {
        $message = 'RPC fault: ' . $message;
        parent::__construct($message, $code, $previous);
    }
}