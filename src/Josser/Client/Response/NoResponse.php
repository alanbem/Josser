<?php

/*
 * This file is part of the Josser package.
 *
 * (C) Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Josser\Client\Response;

use Josser\Client\Response\ResponseInterface;

/**
 * NullObject JSON-RPC response
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
class NoResponse implements ResponseInterface
{
    /**
     * Retrieve response result.
     *
     * @return mixed
     */
    public function getResult()
    {
        return;
    }

    /**
     * Get response id.
     *
     * @return mixed
     */
    public function getId()
    {
        return;
    }
}
