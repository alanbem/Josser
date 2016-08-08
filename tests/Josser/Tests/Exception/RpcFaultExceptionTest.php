<?php

/*
 * This file is part of the Josser package.
 *
 * (C) Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Josser\Tests\Exception;

use Josser\Exception\RpcFaultException;
use Josser\Tests\TestCase as JosserTestCase;

/**
 * Test class for \Josser\Exception\RpcFaultException
 */
class RpcFaultExceptionTest extends JosserTestCase
{
    public function testException()
    {
        $e = new RpcFaultException('message');

        $this->assertEquals('message', $e->getMessage());
        $this->assertEquals(0, $e->getCode());
        $this->assertEquals(null, $e->getData());

        $e = new RpcFaultException('message', 100);

        $this->assertEquals('message', $e->getMessage());
        $this->assertEquals(100, $e->getCode());
        $this->assertEquals(null, $e->getData());

        $e = new RpcFaultException('message', 100, null);

        $this->assertEquals('message', $e->getMessage());
        $this->assertEquals(100, $e->getCode());
        $this->assertEquals(null, $e->getData());

        $previous = new \Exception;
        $e = new RpcFaultException('message', 100, null, $previous);

        $this->assertEquals('message', $e->getMessage());
        $this->assertEquals(100, $e->getCode());
        $this->assertEquals(null, $e->getData());
        $this->assertSame($previous, $e->getPrevious());
    }
}
