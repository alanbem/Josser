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

/**
 * Encoder/Decoder interface.
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
interface EndecInterface
{
    /**
     * Encode $dto to impl format.
     *
     * @abstract
     * @param mixed $dto
     * @return string
     */
    function encode($dto);

    /**
     * Decode impl $format.
     *
     * @abstract
     * @param string $format
     * @return mixed
     */
    function decode($format);
}
