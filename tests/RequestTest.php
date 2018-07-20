<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 19/07/2018
 * Time: 09:46
 */

namespace CwsOps\LivePerson\Tests;

use CwsOps\LivePerson\Account\Config;
use CwsOps\LivePerson\Rest\Request;

use PHPUnit\Framework\TestCase;

/**
 * Class RequestTest
 *
 * @package CwsOps\LivePerson\Tests
 */
class RequestTest extends TestCase
{

    private $config;

    public function setUp()
    {
        $accountId = 'foo';
        $consumerKey = 'bar';
        $consumerSecret = 'biz';
        $token = 'baz';
        $tokenSecret = 'bee';
        $username = 'noo';
        $this->config = new Config($accountId, $consumerKey, $consumerSecret, $token, $tokenSecret, $username);
    }

    /**
     *
     * @covers \CwsOps\LivePerson\Rest\Request::__construct
     */
    public function testCanConstructWithDefaultOptions()
    {
        $request = new Request($this->config);

        $this->assertInstanceOf(Request::class, $request);
    }
}
