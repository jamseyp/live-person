<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 25/07/2018
 * Time: 13:04
 */

namespace CwsOps\LivePerson\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

/**
 * Class MockClient
 * @package CwsOps\LivePerson\Tests
 * @author James Parker <jamseyp@gmail.com>
 */
class MockClient
{
    /**
     * @param Response[] $queue
     *
     * @return Client
     */
    public static function getClient(array $queue)
    {
        $mock = new MockHandler($queue);

        $handler = HandlerStack::create($mock);

        return new Client(['handler' => $handler]);
    }

    public static function createResponse(\stdClass $stdClass)
    {
        return new Response(200, ['X-Foo' => 'Bar'], json_encode($stdClass));
    }
}