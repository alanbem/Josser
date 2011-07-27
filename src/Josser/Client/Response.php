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

use Josser\Client\ResponseInterface;
use Josser\Client\ProtocolInterface;

/**
 * JSON-RPC client response object.
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
class Response implements ResponseInterface
{
    /**
     * @var mixed
     */
    private $id;

    /**
     * @var mixed
     */
    private $result;

    /**
     * Constructor.
     *
     * @param $result
     * @param $id
     */
    public function __construct($result, $id)
    {
        $this->result = $result;
        $this->id     = $id;
    }

    /**
     * Retrieve response result.
     *
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Get response id.
     *
     * @return string|number|null
     */
    public function getId()
    {
        return $this->id;
    }
}
