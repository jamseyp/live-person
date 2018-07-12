<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 12/07/2018
 * Time: 15:22
 */

namespace CwsOps\LivePerson;

/**
 * Class AccountConfig
 * @package CwsOps\LivePerson
 */
class AccountConfig
{
    /** @var string $accountId */
    private $accountId;
    /** @var string $consumerKey */
    private $consumerKey;
    /** @var string $consumerSecret */
    private $consumerSecret;
    /** @var string $token */
    private $token;
    /** @var string $tokenSecret */
    private $tokenSecret;
    /** @var string $username */
    private $username;

    /**
     * AccountConfig constructor.
     * @param string $accountId
     * @param string $consumerKey
     * @param string $consumerSecret
     * @param string $token
     * @param string $tokenSecret
     * @param string $username
     */
    public function __construct(string $accountId, string $consumerKey, string $consumerSecret, string $token, string $tokenSecret, string $username)
    {
        $this->accountId = $accountId;
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
        $this->token = $token;
        $this->tokenSecret = $tokenSecret;
        $this->username = $username;
    }

    /**
     * Gets the account Id
     *
     * @return string
     */
    public function getAccountId(): string
    {
        return $this->accountId;
    }

    /**
     * Gets the consumer key
     *
     * @return string
     */
    public function getConsumerKey(): string
    {
        return $this->consumerKey;
    }

    /**
     * Gets the consumer secret
     *
     * @return string
     */
    public function getConsumerSecret(): string
    {
        return $this->consumerSecret;
    }

    /**
     * Gets the token
     *
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * Gets the token secret
     *
     * @return string
     */
    public function getTokenSecret(): string
    {
        return $this->tokenSecret;
    }

    /**
     * Gets the username
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }
}