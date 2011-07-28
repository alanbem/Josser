<?php

/*
 * This file is part of the Josser package.
 *
 * (C) Alan Gabriel Bem <alan.bem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Josser\Client;

use Josser\Client;
use Josser\Protocol\JsonRpc1 as BitcoinProtocol;
use Josser\Client\Transport\HttpTransport;

/**
 * Bitcoin client.
 *
 * @author Alan Gabriel Bem <alan.bem@gmail.com>
 */
class Bitcoin extends Client
{
    /**
     * bitcoin daemon url
     *
     * @var string
     */
    private $url;

    /**
     * Constructor.
     *
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = $url;
        $transport = new HttpTransport($this->url);
        $protocol = new BitcoinProtocol;
        parent::__construct($transport, $protocol);
    }

    /**
     * Safely copies wallet.dat to $destination, which can be a directory or a path with filename.
     *
     * @param string $destination
     * @return boolean
     */
    public function backupWallet($destination)
    {
        return $this->request('backupwallet', (array) $destination);
    }

    /**
     * Returns the account name associated with the given $address.
     *
     * @param string $address
     * @return string
     */
    public function getAccount($address)
    {
        return $this->request('getaccount', (array) $address);
    }

    /**
     * Returns the current bitcoin address for receiving payments to this $account.
     *
     * @param string $account
     * @return string
     */
    public function getAccountAddress($account)
    {
        return $this->request('getaccountaddress', (array) $account);
    }

    /**
     * Returns an array of addresses for the given $account name.
     *
     * @param string $account
     * @return array
     */
    public function getAddressesByAccount($account)
    {
        return $this->request('getaddressesbyaccount', (array) $account);
    }

    /**
     * If $account name is not specified, returns the server's total available balance. If $account name is specified, returns
     * the balance of that account.
     *
     * @param string $account
     * @param int $minimumNumberOfConfirmations
     * @return float
     */
    public function getBalance($account = null, $minimumNumberOfConfirmations = 1)
    {
        if(null === $account) {
            return $this->request('getbalance');
        }
        return $this->request('getbalance', array($account, $minimumNumberOfConfirmations));
    }

    /**
     * Returns the number of connections to other nodes.
     *
     * @return integer
     */
    public function getConnectionCount()
    {
        return $this->request('getconnectioncount');
    }

    /**
     * Returns a new bitcoin address for receiving payments.  If $account name is specified (recommended), it is added to
     * the address book so payments received with the address will be credited to $account.
     *
     * @param string $account
     * @return string
     */
    public function getNewAddress($account)
    {
        return $this->request('getnewaddress', (array) $account);
    }

    /**
     * Returns the total amount received by addresses with $account name in transactions with at least $minimumNumberOfConfirmations.
     *
     * @param string $account
     * @param int $minimumNumberOfConfirmations
     * @return float
     */
    public function getReceivedByAccount($account, $minimumNumberOfConfirmations = 1)
    {
        return $this->request('getreceivedbyaccount', array($account, $minimumNumberOfConfirmations));
    }

    /**
     * Returns the total amount received by $address in transactions with at least $minimumNumberOfConfirmations.
     *
     * @param string $address
     * @param int $minimumNumberOfConfirmations
     * @return float
     */
    public function getReceivedByAddress($address, $minimumNumberOfConfirmations = 1)
    {
        return $this->request('getreceivedbyaddress', array($address, $minimumNumberOfConfirmations));
    }

    /**
     * Returns an array of accounts.
     *
     * @param int $minimumNumberOfConfirmations
     * @return array
     */
    public function listAccounts($minimumNumberOfConfirmations = 1)
    {
        return $this->request('listaccounts', (array) $minimumNumberOfConfirmations);
    }

    /**
     * Returns an array of objects containing:
     *   - "account" : the account name of the receiving addresses
     *   - "amount" : total amount received by addresses with this account
     *   - "confirmations" : number of confirmations of the most recent transaction included
     *
     * @param int $minimumNumberOfConfirmations
     * @param bool $includeEmpty
     * @return array
     */
    public function listReceivedByAccount($minimumNumberOfConfirmations = 1, $includeEmpty = false)
    {
        return $this->request('listreceivedbyaccount', array($minimumNumberOfConfirmations, $includeEmpty));
    }

    /**
     * Returns an array of objects containing:
     *   - "address" : receiving address
     *   - "account" : the account of the receiving address
     *   - "amount" : total amount received by the address
     *   - "confirmations" : number of confirmations of the most recent transaction included
     *
     * @param int $minimumNumberOfConfirmations
     * @param bool $includeEmpty
     * @return array
     */
    public function listReceivedByAddress($minimumNumberOfConfirmations = 1, $includeEmpty = false)
    {
        return $this->request('listreceivedbyaddress', array($minimumNumberOfConfirmations, $includeEmpty));
    }

    /**
     * Returns up to $count most recent transactions skipping the first $from transactions for $account.
     *
     * @param string $account
     * @param int $count
     * @param int $from
     * @return array
     */
    public function listTransactions($account, $count = 10, $from = 0)
    {
        return $this->request('listtransactions', array($account, $count, $from));
    }

    /**
     * Move $from one account in your wallet $to another.
     *
     * @param string $from
     * @param string $to
     * @param float $amount
     * @param int $minimumNumberOfConfirmations
     * @return mixed
     */
    public function move($from, $to, $amount, $minimumNumberOfConfirmations = 1)
    {
        return $this->request('move', array($from, $to, $amount, $minimumNumberOfConfirmations));
    }

    /**
     * Send $amount of bitcoins from $account in your wallet to $address. Return transaction id on success.
     *
     * @param string $account
     * @param string $address
     * @param float $amount
     * @param int $minimumNumberOfConfirmations
     * @return string
     */
    public function sendFrom($account, $address, $amount, $minimumNumberOfConfirmations = 1)
    {
        return $this->request('sendfrom', array($account, $address, $amount, $minimumNumberOfConfirmations));
    }

    /**
     * Send $amount of bitcoins from you wallet to $address. Return transaction id on success.
     *
     * @abstract
     * @param string $address
     * @param float $amount
     * @return string
     */
    public function sendToAddress($address, $amount)
    {
        return $this->request('sendtoaddress', array($address, $amount));
    }

    /**
     * Sets the $account associated with the given $address.
     *
     * @param string $address
     * @param string $account
     * @return void
     */
    public function setAccount($address, $account)
    {
        return $this->request('setaccount', array($address, $account));
    }

}
