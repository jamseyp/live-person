<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 20/07/2018
 * Time: 20:24
 */

namespace CwsOps\LivePerson\Tests;

use CwsOps\LivePerson\Account\Config;
use CwsOps\LivePerson\Services\AbstractService;
use CwsOps\LivePerson\Services\RequestNotSentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class AbstractServiceTest
 *
 * @package CwsOps\LivePerson\Tests
 */
class AbstractServiceTest extends TestCase
{

    /** @var MockObject|AbstractService */
    private $mock;
    /** @var Config */
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

        $this->mock = $this->getMockBuilder(AbstractService::class)
            ->disableOriginalConstructor()
            ->setMethods(['getStatus', 'getResponse'])
            ->getMockForAbstractClass();

    }

    /**
     * @covers \CwsOps\LivePerson\Services\AbstractService::__construct
     */
    public function testCanInitWithOptions()
    {
        $mock = $this->getMockBuilder(AbstractService::class)
            ->setConstructorArgs([$this->config, 5, null])
            ->getMockForAbstractClass();

        $this->assertInstanceOf(AbstractService::class, $mock);
    }

    /**
     * @covers \CwsOps\LivePerson\Services\AbstractService::__construct
     */
    public function testWillThrowInvalidArgumentOnInvalidRetryLimit()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Maximum $retryLimit is 5 you tried setting 10, try setting a value between 0 and 5');

        new class($this->config, 10) extends AbstractService
        {
            protected function getDomain(): string
            {
                return 'foo';
            }
        };
    }

    /**
     * @covers \CwsOps\LivePerson\Services\AbstractService::getStatus()
     */
    public function testGetStatus()
    {
        $status = new \stdClass();
        $status->live = true;

        $this->mock->expects($this->once())
            ->method('getStatus')
            ->willReturn($status);

        $result = $this->mock->getStatus();

        $this->assertInstanceOf(\stdClass::class, $result);
        $this->assertTrue($status->live);
    }

    /**
     * @covers \CwsOps\LivePerson\Services\AbstractService::getResponse()
     *
     * @throws RequestNotSentException
     */
    public function testGetResponse()
    {
        $response = new \stdClass();
        $response->data = ['foo' => 'bar'];
        $response->bool = false;

        $this->mock->expects($this->once())
            ->method('getResponse')
            ->willReturn($response);

        $result = $this->mock->getResponse();

        $this->assertInstanceOf(\stdClass::class, $result);
        $this->assertFalse($result->bool);
        $this->assertArrayHasKey('foo', $result->data);

    }

    /**
     *
     * @covers \CwsOps\LivePerson\Services\AbstractService::getResponse()
     */
    public function testWillThrowNotBuiltOnNoRequest()
    {
        /** @var AbstractService|MockObject $mock */
        $mock = $this->getMockBuilder(AbstractService::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->expectException(RequestNotSentException::class);

        $mock->getResponse();
    }

    public function tearDown()
    {
        $this->mock = null;
    }


}
