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

/**
 * RPC fault exception.
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
class RpcFaultException extends \RuntimeException implements JosserException
{
    /**
     * @var mixed
     */
    private $data;

    public function __construct($message, $code = 0, $data = null, \Exception $previous = null)
    {
        $this->data = $data;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}
