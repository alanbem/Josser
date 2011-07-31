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
use Josser\Client\Request\RequestInterface;
use Josser\Client\Response\ResponseInterface;

/**
 * Request/Response mismatch exception.
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
class RequestResponseMismatchException extends \RuntimeException implements JosserException
{
    public function __construct(RequestInterface $request, ResponseInterface $response)
    {
        $error = sprintf('Response id (%s) does not match request id (%s).', (string) $response->getId(), (string) $request->getId());
        parent::__construct($error);
    }

}
