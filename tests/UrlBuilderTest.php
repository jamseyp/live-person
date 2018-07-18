<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 12/07/2018
 * Time: 21:45
 */

namespace CwsOps\LivePerson\Tests;

use CwsOps\LivePerson\Rest\BuilderLockedException;
use CwsOps\LivePerson\Rest\UrlBuilder;
use CwsOps\LivePerson\Rest\URLNotBuiltException;
use PHPUnit\Framework\TestCase;

/**
 * Class UrlBuilderTest
 *
 * @package CwsOps\LivePerson\Tests
 */
class UrlBuilderTest extends TestCase
{
    /**
     * @covers \CwsOps\LivePerson\UrlBuilder::__construct
     */
    public function testCanInitWithNoOptions()
    {
        $builder = new UrlBuilder();

        $this->assertInstanceOf(UrlBuilder::class, $builder);
    }

    /**
     * @covers \CwsOps\LivePerson\UrlBuilder::create()
     */
    public function testReturnsInstanceOfSelf()
    {
        $builder = new UrlBuilder();

        $this->assertInstanceOf(UrlBuilder::class, $builder->create(true));
    }

    /**
     * @covers \CwsOps\LivePerson\UrlBuilder::build()
     * @covers \CwsOps\LivePerson\UrlBuilder::getUrl()
     * @covers \CwsOps\LivePerson\UrlBuilder::create()
     */
    public function testCanBuildAndCreate()
    {
        $builder = new UrlBuilder();
        $builder->create();

        $this->assertStringStartsWith('http', $builder->build()->getUrl());
    }

    /**
     * @covers \CwsOps\LivePerson\UrlBuilder::build()
     * @covers \CwsOps\LivePerson\UrlBuilder::getUrl()
     * @covers \CwsOps\LivePerson\UrlBuilder::create()
     */
    public function testCanBuildSecureUrl()
    {
        $builder = new UrlBuilder();
        $builder->create();

        $this->assertStringStartsWith('https', $builder->build()->getUrl());
    }

    /**
     * @covers \CwsOps\LivePerson\UrlBuilder::setDomain()
     */
    public function testCanSetDomain()
    {
        $builder = new UrlBuilder();
        $builder->create();
        $builder->setDomain('testDomain');

        $this->assertContains('testDomain', $builder->build()->getUrl());
    }

    /**
     * @covers \CwsOps\LivePerson\UrlBuilder::setService()
     */
    public function testCanSetService()
    {
        $builder = new UrlBuilder();
        $builder->create();
        $builder->setService('testService');

        $this->assertContains('testService', $builder->build()->getUrl());
    }

    /**
     * @covers \CwsOps\LivePerson\UrlBuilder::setAction()
     */
    public function testCanSetAction()
    {
        $builder = new UrlBuilder();
        $builder->create();
        $builder->setAction('testAction');

        $this->assertContains('testAction', $builder->build()->getUrl());
    }


    /**
     * @covers \CwsOps\LivePerson\UrlBuilder::setAccount()
     */
    public function testCanSetAccount()
    {
        $builder = new UrlBuilder();
        $builder->create();
        $builder->setAccount('128182');

        $this->assertContains('128182', $builder->build()->getUrl());
    }

    /**
     * @covers \CwsOps\LivePerson\UrlBuilder::addActionContext()
     */
    public function testCanSetActionContext()
    {
        $builder = new UrlBuilder();
        $builder->create();
        $builder->addActionContext('testActionContext');

        $this->assertContains('testActionContext', $builder->build()->getUrl());
    }

    /**
     * @covers \CwsOps\LivePerson\UrlBuilder::addQueryParam()
     * @covers \CwsOps\LivePerson\UrlBuilder::hasQueryParam()
     */
    public function testCanAddAdditionalParams()
    {
        $builder = new UrlBuilder();
        $builder->create();
        $builder->hasQueryParam(true);
        $builder->addQueryParam('foo', 'bar');

        $this->assertContains('?foo=bar', $builder->build()->getUrl());
    }

    /**
     * @covers \CwsOps\LivePerson\UrlBuilder::hasQueryParam()
     */
    public function testCanSetHasQueryParamsToFalse()
    {
        $builder = new UrlBuilder();
        $builder->create();
        $builder->hasQueryParam(false);
        $builder->addQueryParam('foo', 'bar');

        $this->assertNotContains('?foo=bar', $builder->build()->getUrl());
    }

    /**
     * @covers \CwsOps\LivePerson\UrlBuilder::setVersion()
     */
    public function testCanChangeVersionNumber()
    {
        $builder = new UrlBuilder();
        $builder->create();
        $builder->setVersion(22);

        $this->assertContains('?v=22', $builder->build()->getUrl());
    }

    /**
     * @covers \CwsOps\LivePerson\UrlBuilder::build()
     * @covers \CwsOps\LivePerson\UrlBuilder::getUrl()
     */
    public function testCanBuildFullUrl()
    {
        $builder = new UrlBuilder();

        $url = $builder->create(true)
            ->setDomain('en.liveperson.net')
            ->setService('message-history')
            ->setAccount('1929283')
            ->addActionContext('foo')
            ->hasQueryParam(true)
            ->addQueryParam('foo', 'bar')
            ->setVersion('2912')
            ->build()
            ->getUrl();

        $expected = 'https://en.liveperson.net/message-history/api/account/1929283/foo?foo=barv=2912';

        $this->assertEquals($expected, $url);
    }

    public function testThrowsBuilderLockedException()
    {
        $builder = new UrlBuilder();

        $url = $builder->create(true)
            ->setDomain('en.liveperson.net')
            ->setService('message-history')
            ->setAccount('1929283')
            ->addActionContext('foo')
            ->hasQueryParam(true)
            ->addQueryParam('foo', 'bar')
            ->setVersion('2912')
            ->build();

        $this->expectException(BuilderLockedException::class);

        $url->setAction('s');
    }

    public function testThrowsUrlNotBuiltException()
    {
        $builder = new UrlBuilder();

        $this->expectException(URLNotBuiltException::class);

        $builder->getUrl();
    }
}
