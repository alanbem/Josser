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

use Josser\Client\Protocol\Protocol;

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
     * @return mixed
     */
    public function getId();
}
