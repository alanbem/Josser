<?php

/*
 * This file is part of the Josser package.
 *
 * (C) Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Josser\Endec;

use Josser\Endec\EndecInterface;

/**
 * Basic JSON Encoder/Decoder.
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
class BasicJsonEndec implements EndecInterface
{
    /**
     * Encode $dto to JSON.
     *
     * @param mixed $dto
     * @return string
     */
    function encode($dto)
    {
        return json_encode($dto);
    }

    /**
     * Decode JSON.
     *
     * @param string $json
     * @return mixed
     */
    function decode($json)
    {
        return json_decode($json, true);
    }
}