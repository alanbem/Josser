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
     * CURLOPT_VERBOSE
     *
     * @var bool
     */
    private $verbose = false;

    /**
     * Constructor.
     *
     * @param string $url
     * @param bool $verbose
     */
    public function __construct($url, $verbose = false)
    {
        $this->url = $url;
        $this->verbose = $verbose;
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
     * @captains-log CURL is the most sufficient to this job. I've tried file_put_contents (with http stream context), but in case of an error I didn't get response with error object.
     *
     * @throws \Josser\Exception\TransportFailedException
     * @param mixed $data
     * @return string
     */
    function send($data)
    {
        $curl = curl_init($this->url);
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        
        curl_setopt($curl, CURLOPT_VERBOSE, $this->verbose);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $body = curl_exec($curl);
        if (false === $body) {
            $error = sprintf('JSON-RPC http connection failed. Remote service at "%s" is not responding.', $this->url);
            throw new TransportFailureException($error);
        }
        curl_close($curl);
        return $body;
    }
}
