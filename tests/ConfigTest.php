<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 18/07/2018
 * Time: 12:49
 */

namespace CwsOps\LivePerson\Tests;

use CwsOps\LivePerson\Account\Config;

use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{

    /**
     * @covers \CwsOps\LivePerson\Account\Config::__construct
     */
    public function testCanConstruct()
    {
        $accountId = 'foo';
        $consumerKey = 'bar';
        $consumerSecret = 'biz';
        $token = 'baz';
        $tokenSecret = 'bee';
        $username = 'noo';
        $config = new Config($accountId, $consumerKey, $consumerSecret, $token, $tokenSecret, $username);

        $this->assertInstanceOf(Config::class, $config);
    }

    /**
     * @covers \CwsOps\LivePerson\Account\Config::__construct
     */
    public function testCantConstructWithoutDefaultArgs()
    {
        $this->expectException(\ArgumentCountError::class);

        $config = new Config('foo', 'bar');
    }

    /**
     * @covers \CwsOps\LivePerson\Account\Config::getUsername()
     */
    public function testGetUsername()
    {
        $accountId = 'foo';
        $consumerKey = 'bar';
        $consumerSecret = 'biz';
        $token = 'baz';
        $tokenSecret = 'bee';
        $username = 'noo';
        $config = new Config($accountId, $consumerKey, $consumerSecret, $token, $tokenSecret, $username);


        $this->assertInstanceOf(Config::class, $config);

        $this->assertEquals($username, $config->getUsername());
    }

    /**
     * @covers \CwsOps\LivePerson\Account\Config::getConsumerKey()
     */
    public function testGetConsumerKey()
    {
        $accountId = 'foo';
        $consumerKey = 'bar';
        $consumerSecret = 'biz';
        $token = 'baz';
        $tokenSecret = 'bee';
        $username = 'noo';
        $config = new Config($accountId, $consumerKey, $consumerSecret, $token, $tokenSecret, $username);

        $this->assertInstanceOf(Config::class, $config);

        $this->assertEquals($consumerKey, $config->getConsumerKey());
    }

    /**
     * @covers \CwsOps\LivePerson\Account\Config::getAccountId()
     */
    public function testGetAccountId()
    {
        $accountId = 'foo';
        $consumerKey = 'bar';
        $consumerSecret = 'biz';
        $token = 'baz';
        $tokenSecret = 'bee';
        $username = 'noo';
        $config = new Config($accountId, $consumerKey, $consumerSecret, $token, $tokenSecret, $username);

        $this->assertInstanceOf(Config::class, $config);

        $this->assertEquals($accountId, $config->getAccountId());
    }

    /**
     * @covers \CwsOps\LivePerson\Account\Config::getTokenSecret()
     */
    public function testGetTokenSecret()
    {
        $accountId = 'foo';
        $consumerKey = 'bar';
        $consumerSecret = 'biz';
        $token = 'baz';
        $tokenSecret = 'bee';
        $username = 'noo';
        $config = new Config($accountId, $consumerKey, $consumerSecret, $token, $tokenSecret, $username);
        $this->assertInstanceOf(Config::class, $config);

        $this->assertEquals($tokenSecret, $config->getTokenSecret());
    }

    /**
     * @covers \CwsOps\LivePerson\Account\Config::getConsumerSecret()
     */
    public function testGetConsumerSecret()
    {
        $accountId = 'foo';
        $consumerKey = 'bar';
        $consumerSecret = 'biz';
        $token = 'baz';
        $tokenSecret = 'bee';
        $username = 'noo';
        $config = new Config($accountId, $consumerKey, $consumerSecret, $token, $tokenSecret, $username);

        $this->assertInstanceOf(Config::class, $config);

        $this->assertEquals($consumerSecret, $config->getConsumerSecret());
    }

    /**
     * @covers \CwsOps\LivePerson\Account\Config::getToken()
     */
    public function testGetToken()
    {
        $accountId = 'foo';
        $consumerKey = 'bar';
        $consumerSecret = 'biz';
        $token = 'baz';
        $tokenSecret = 'bee';
        $username = 'noo';
        $config = new Config($accountId, $consumerKey, $consumerSecret, $token, $tokenSecret, $username);

        $this->assertInstanceOf(Config::class, $config);

        $this->assertEquals($token, $config->getToken());
    }
}
