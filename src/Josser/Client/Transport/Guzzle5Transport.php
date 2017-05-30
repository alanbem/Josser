<?php

/*
 * This file is part of the Josser package.
 *
 * (C) Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Josser\Client\Transport;

use GuzzleHttp\Client;
use Josser\Exception\TransportFailureException;

/**
 * JSON-RPC http transport with Guzzle 5.
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
class Guzzle5Transport implements TransportInterface
{
    /**
     * Guzzle http client.
     *
     * @var Client
     */
    private $guzzle;

    /**
     * URI of the web service, or null to use the base_uri of the Guzzle http client.
     *
     * @var string|null
     */
    private $uri;

    /**
     * @param Client $guzzle
     * @param string|null $uri URL of the web service, or null to use the base_uri of $guzzle
     */
    public function __construct(Client $guzzle, $uri = null)
    {
        $this->guzzle = $guzzle;
        $this->uri = $uri;
    }

    /**
     * @return Client
     */
    public function getGuzzle()
    {
        return $this->guzzle;
    }

    /**
     * Send data to remote JSON-RPC service over HTTP.
     *
     * @throws \Josser\Exception\TransportFailureException
     * @param mixed $data
     * @return string
     */
    public function send($data)
    {
        try {
            $response = $this->guzzle->post($this->uri, [
                'body' => $data,
                'headers' => [
                    'Content-Type' => 'application/json',
                ]
            ]);
            return $response->getBody()->getContents();
        } catch (\Exception $e) {
            $error = sprintf('JSON-RPC http connection failed. Remote service at "%s" is not responding.', $this->guzzle->getBaseUrl());
            throw new TransportFailureException($error, null, $e);
        }
    }
}
