<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 26/07/2018
 * Time: 10:18
 */

namespace CwsOps\LivePerson\Tests;

use CwsOps\LivePerson\Account\Config;
use CwsOps\LivePerson\Rest\Request;
use CwsOps\LivePerson\Services\EngagementService;
use PHPUnit\Framework\TestCase;

class EngagementServiceTest extends TestCase
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

        $service = new EngagementService($this->config);

        $res1 = new \stdClass();
        $res1->baseUri = 'test';

        $service->getRequest()->setClient(MockClient::getClient([
            MockClient::createResponse($res1)
        ]));

        $this->assertInstanceOf(EngagementService::class, $service);
        $this->assertInstanceOf(Request::class, $service->getRequest());
    }
}
