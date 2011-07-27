<?php

/*
 * This file is part of the Josser package.
 *
 * (c) Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__.'/Josser/TestCase.php';

spl_autoload_register(function($class)
{
    $file = __DIR__.'/../src/'.strtr($class, '\\', '/').'.php';
    if (file_exists($file)) {
        require $file;
        return true;
    }
});
