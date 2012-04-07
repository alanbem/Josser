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

use Buzz\Browser;
use Josser\Client\Transport\TransportInterface;
use Josser\Exception\TransportFailureException;

/**
 * JSON-RPC http transport.
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
class HttpTransport implements TransportInterface
{
    /**
     * Remote JSON-RPC service.
     * 
     * @var string
     */
    private $url;

    /**
     * Buzz http client.
     *
     * @var \Buzz\Browser
     */
    private $browser;

    /**
     * Constructor.
     *
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * Factory method for creating http transport object.
     *
     * @static
     *
     * @param string $host
     * @param string $user
     * @param string $password
     * @param int    $port
     * @param bool   $isSecure    Whether secure connection should be used
     *
     * @return \Josser\Client\Transport\HttpTransport
     */
    public static function create($host, $user, $password, $port = 8332, $isSecure = false)
    {
        $url = self::buildUrl((string)$host, (string)$user, (string)$password, (integer)$port, (boolean)$isSecure);

        return new HttpTransport($url);
    }

    /**
     * Builds http url.
     *
     * @static
     *
     * @param string $host
     * @param string $user
     * @param string $password
     * @param int    $port
     * @param bool   $isSecure    Indicates whether http or https protocol should be used.
     *
     * @return string
     */
    private static function buildUrl($host, $user, $password, $port, $isSecure)
    {
        if ((bool) $isSecure) {
            $scheme = 'https';
        } else {
            $scheme = 'http';
        }

        $url = '%s://%s:%s@%s:%d';
        $url = sprintf($url, $scheme, $user, $password, $host, $port);

        return $url;
    }

    /**
     * Get Buzz http client.
     *
     * @return \Buzz\Browser
     */
    public function getBrowser()
    {
        if(!isset($this->browser)) {
            $this->browser = new Browser();
        }

        return $this->browser;
    }

    /**
     * Set Buzz http client.
     *
     * @param \Buzz\Browser $browser
     * @return \Josser\Client\Transport\HttpTransport
     */
    public function setBrowser(Browser $browser)
    {
        $this->browser = $browser;
        return $this;
    }

    /**
     * Get remote JSON-RPC service url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Send data to remote JSON-RPC service over HTTP.
     *
     * @throws \Josser\Exception\TransportFailureException
     * @param mixed $data
     * @return string
     */
    function send($data)
    {
        try {
            $headers = array('Content-Type: application/json');
            $response = $this->browser->post($this->getUrl(), $headers, $data)->getContent();
            return $response;
        } catch (\Exception $e) {
            $error = sprintf('JSON-RPC http connection failed. Remote service at "%s" is not responding.', $this->url);
            throw new TransportFailureException($error, null, $e);
        }
    }
}
