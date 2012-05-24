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

/**
 * JSON-RPC response interface.
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
interface ResponseInterface
{
    /**
     * Retrieve response result.
     *
     * @abstract
     * @return mixed
     */
    public function getResult();

    /**
     * Get response id.
     *
     * @abstract
     * @return mixed
     */
    public function getId();
}
