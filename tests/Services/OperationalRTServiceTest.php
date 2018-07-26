<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 25/07/2018
 * Time: 13:12
 */

namespace CwsOps\LivePerson\Tests;

use CwsOps\LivePerson\Account\Config;
use CwsOps\LivePerson\Rest\Request;
use CwsOps\LivePerson\Services\OperationalRTService;
use PHPUnit\Framework\TestCase;

class OperationalRTServiceTest extends TestCase
{
    private $config;

    public function setUp()
    {
        require_once __DIR__ . '/../Services/MockClient.php';

        $accountId = 'foo';
        $consumerKey = 'bar';
        $consumerSecret = 'biz';
        $token = 'baz';
        $tokenSecret = 'bee';
        $username = 'noo';
        $this->config = new Config($accountId, $consumerKey, $consumerSecret, $token, $tokenSecret, $username);
    }

    /**
     * @covers \CwsOps\LivePerson\Services\AbstractService::__construct()
     * @covers \CwsOps\LivePerson\Services\AbstractService::getRequest()
     */
    public function testCanConstruct()
    {
        $accountId = 'foo';
        $consumerKey = 'bar';
        $consumerSecret = 'biz';
        $token = 'baz';
        $tokenSecret = 'bee';
        $username = 'noo';
        $this->config = new Config($accountId, $consumerKey, $consumerSecret, $token, $tokenSecret, $username);

        $service = new OperationalRTService($this->config);

        $res1 = new \stdClass();
        $res1->baseUri = 'test';

        $service->getRequest()->setClient(MockClient::getClient([
            MockClient::createResponse($res1)
        ]));

        $this->assertInstanceOf(OperationalRTService::class, $service);
        $this->assertInstanceOf(Request::class, $service->getRequest());
    }

    /**
     * @covers \CwsOps\LivePerson\Services\OperationalRTService::getResponse()
     *
     * @throws \CwsOps\LivePerson\Rest\BuilderLockedException
     * @throws \CwsOps\LivePerson\Rest\URLNotBuiltException
     * @throws \CwsOps\LivePerson\Services\RequestNotSentException
     */
    public function testResponseInstanceOfStdClass()
    {
        $service = new OperationalRTService($this->config);

        $res1 = new \stdClass();
        $res1->baseUri = 'test';

        $service->getRequest()->setClient(MockClient::getClient([
            MockClient::createResponse($res1),
            MockClient::createResponse($res1)
        ]));

        $service->agentActivity(60);

        $this->assertInstanceOf(\stdClass::class, $service->getResponse());
    }

    /**
     * @covers \CwsOps\LivePerson\Services\OperationalRTService::isTimeFrameValid()
     */
    public function testAgentActivityThrowsExceptionOnInvalidTimeFrame()
    {
        $service = new OperationalRTService($this->config);

        $res1 = new \stdClass();
        $res1->baseUri = 'test';


        $service->getRequest()->setClient(MockClient::getClient([
            MockClient::createResponse($res1),
            MockClient::createResponse($res1)
        ]));


        try {
            $service->agentActivity(5000);
        } catch (\Exception $e) {
            $this->assertInstanceOf(\InvalidArgumentException::class, $e);
            $this->assertEquals('The $timeframe must be between 0 and 1440, you passed 5000', $e->getMessage());
        }
        try {
            $service->agentActivity(-1);
        } catch (\Exception $e) {
            $this->assertInstanceOf(\InvalidArgumentException::class, $e);
            $this->assertEquals('The $timeframe must be between 0 and 1440, you passed -1', $e->getMessage());
        }
    }

    /**
     * @covers \CwsOps\LivePerson\Services\OperationalRTService::isIntervalValid()
     */
    public function testThrowsExceptionOnInvalidInterval()
    {
        $service = new OperationalRTService($this->config);

        $res1 = new \stdClass();
        $res1->baseUri = 'test';

        $service->getRequest()->setClient(MockClient::getClient([
            MockClient::createResponse($res1),
            MockClient::createResponse($res1)
        ]));


        try {
            // Interval larger, but dividable by 60, Should fail.
            $service->agentActivity(60, [12345, 12345], 120);
        } catch (\Exception $e) {
            $this->assertInstanceOf(\InvalidArgumentException::class, $e);
            $this->assertEquals('The $interval you passed was not valid or not dividable by the $timeframe (60), you passed 120', $e->getMessage());
        }
        try {
            // Interval smaller, but not dividable by 60, Should fail.
            $service->agentActivity(60, [12345, 12345], 34);
        } catch (\Exception $e) {
            $this->assertInstanceOf(\InvalidArgumentException::class, $e);
            $this->assertEquals('The $interval you passed was not valid or not dividable by the $timeframe (60), you passed 34', $e->getMessage());
        }
    }
}
