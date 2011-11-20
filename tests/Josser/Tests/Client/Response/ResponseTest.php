<?php

/*
 * This file is part of the Josser package.
 *
 * (C) Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Josser\Tests\Client\Response;

use Josser\Tests\TestCase as JosserTestCase;
use Josser\Client\Response\Response;

/**
 * Test class for Josser\Client\Response\Response.
 */
class ResponseTest extends JosserTestCase
{
    /**
     * Test response object.
     *
     * @param mixed $result
     * @param mixed $id
     * @return void
     *
     * @dataProvider responseDataProvider
     * @covers \Josser\Client\Response\Response::getResult
     * @covers \Josser\Client\Response\Response::getId
     */
    public function testRequest($result, $id)
    {
        $response = new Response($result, $id);

        $this->assertEquals($result, $response->getResult());
        $this->assertEquals($id, $response->getId());
    }

    /**
     * Fixtures
     *
     * @return array
     */
    public function responseDataProvider()
    {
        return array(
            array('', 1),
            array('', 'asd'),
            array(213, 1),
            array(213, 'asd'),
            array(2.13, 1),
            array(2.13, 'asd'),
        );
    }
}