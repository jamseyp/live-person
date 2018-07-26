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
 * @codeCoverageIgnore
 * Class UrlBuilderTest
 *
 *
 *
 * @coversDefaultClass \CwsOps\LivePerson\Rest\UrlBuilder
 *
 * @package CwsOps\LivePerson\Tests
 */
class UrlBuilderTest extends TestCase
{

    public function testCanInitWithNoOptions()
    {
        $builder = new UrlBuilder();

        $this->assertInstanceOf(UrlBuilder::class, $builder);
    }

    /**
     * @covers \CwsOps\LivePerson\Rest\UrlBuilder::create()
     *
     * @throws BuilderLockedException
     */
    public function testReturnsInstanceOfSelf()
    {
        $builder = new UrlBuilder();

        $this->assertInstanceOf(UrlBuilder::class, $builder->create(true));
    }

    /**
     * @covers \CwsOps\LivePerson\Rest\UrlBuilder::build()
     * @covers \CwsOps\LivePerson\Rest\UrlBuilder::getUrl()
     * @covers \CwsOps\LivePerson\Rest\UrlBuilder::create()
     *
     * @throws BuilderLockedException
     * @throws URLNotBuiltException
     */
    public function testCanBuildAndCreate()
    {
        $builder = new UrlBuilder();
        $builder->create();

        $this->assertStringStartsWith('http', $builder->build()->getUrl());
    }

    /**
     * @covers \CwsOps\LivePerson\Rest\UrlBuilder::build()
     * @covers \CwsOps\LivePerson\Rest\UrlBuilder::getUrl()
     * @covers \CwsOps\LivePerson\Rest\UrlBuilder::create()
     *
     * @throws BuilderLockedException
     * @throws URLNotBuiltException
     */
    public function testCanBuildSecureUrl()
    {
        $builder = new UrlBuilder();
        $builder->create();

        $this->assertStringStartsWith('https', $builder->build()->getUrl());
    }

    /**
     * @covers \CwsOps\LivePerson\Rest\UrlBuilder::setDomain()
     *
     * @throws BuilderLockedException
     * @throws URLNotBuiltException
     */
    public function testCanSetDomain()
    {
        $builder = new UrlBuilder();
        $builder->create();
        $builder->setDomain('testDomain');

        $this->assertContains('testDomain', $builder->build()->getUrl());
    }

    /**
     * @covers \CwsOps\LivePerson\Rest\UrlBuilder::setService()
     *
     * @throws BuilderLockedException
     * @throws URLNotBuiltException
     */
    public function testCanSetService()
    {
        $builder = new UrlBuilder();
        $builder->create();
        $builder->setService('testService');

        $this->assertContains('testService', $builder->build()->getUrl());
    }

    /**
     * @covers \CwsOps\LivePerson\Rest\UrlBuilder::setAction()
     *
     * @throws BuilderLockedException
     * @throws URLNotBuiltException
     */
    public function testCanSetAction()
    {
        $builder = new UrlBuilder();
        $builder->create();
        $builder->setAction('testAction');

        $this->assertContains('testAction', $builder->build()->getUrl());
    }


    /**
     * @covers \CwsOps\LivePerson\Rest\UrlBuilder::setAccount()
     *
     * @throws BuilderLockedException
     * @throws URLNotBuiltException
     */
    public function testCanSetAccount()
    {
        $builder = new UrlBuilder();
        $builder->create();
        $builder->setAccount('128182');

        $this->assertContains('128182', $builder->build()->getUrl());
    }

    /**
     * @covers \CwsOps\LivePerson\Rest\UrlBuilder::addActionContext()
     *
     * @throws BuilderLockedException
     * @throws URLNotBuiltException
     */
    public function testCanSetActionContext()
    {
        $builder = new UrlBuilder();
        $builder->create();
        $builder->addActionContext('testActionContext');

        $this->assertContains('testActionContext', $builder->build()->getUrl());
    }

    /**
     * @covers \CwsOps\LivePerson\Rest\UrlBuilder::addQueryParam()
     * @covers \CwsOps\LivePerson\Rest\UrlBuilder::hasQueryParam()
     *
     * @throws BuilderLockedException
     * @throws URLNotBuiltException
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
     * @covers \CwsOps\LivePerson\Rest\UrlBuilder::hasQueryParam()
     * @covers \CwsOps\LivePerson\Rest\UrlBuilder::addQueryParam()
     *
     * @throws BuilderLockedException
     * @throws URLNotBuiltException
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
     * @covers \CwsOps\LivePerson\Rest\UrlBuilder::setVersion()
     * @covers \CwsOps\LivePerson\Rest\UrlBuilder::createUrl()
     *
     * @throws BuilderLockedException
     * @throws URLNotBuiltException
     */
    public function testCanChangeVersionNumber()
    {
        $builder = new UrlBuilder();
        $builder->create();
        $builder->setVersion(22);

        $this->assertContains('?v=22', $builder->build()->getUrl());
    }

    /**
     * @covers \CwsOps\LivePerson\Rest\UrlBuilder::build()
     * @covers \CwsOps\LivePerson\Rest\UrlBuilder::getUrl()
     * @covers \CwsOps\LivePerson\Rest\UrlBuilder::createUrl()
     *
     * @throws BuilderLockedException
     * @throws URLNotBuiltException
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

    /**
     * @covers \CwsOps\LivePerson\Rest\UrlBuilder::isUrlBuilt()
     * @throws BuilderLockedException
     */
    public function testCanCheckIfUrlIsBuilt()
    {
        $builder = new UrlBuilder();

        $this->assertFalse($builder->isUrlBuilt());

        $builder->create(true)
            ->setDomain('en.liveperson.net')
            ->setService('message-history')
            ->setAccount('1929283')
            ->addActionContext('foo')
            ->hasQueryParam(true)
            ->addQueryParam('foo', 'bar')
            ->setVersion('2912')
            ->build();

        $this->assertTrue($builder->isUrlBuilt());
    }

    /**
     * @covers \CwsOps\LivePerson\Rest\BuilderLockedException
     * @covers \CwsOps\LivePerson\Rest\UrlBuilder::isValid()
     * @covers \CwsOps\LivePerson\Rest\UrlBuilder::addToUrl()
     * @covers \CwsOps\LivePerson\Rest\UrlBuilder::setAction()
     * @covers \CwsOps\LivePerson\Rest\UrlBuilder::setService()
     * @covers \CwsOps\LivePerson\Rest\UrlBuilder::setAccount()
     * @covers \CwsOps\LivePerson\Rest\UrlBuilder::hasQueryParam()
     * @covers \CwsOps\LivePerson\Rest\UrlBuilder::addQueryParam()
     * @covers \CwsOps\LivePerson\Rest\UrlBuilder::setVersion()
     * @covers \CwsOps\LivePerson\Rest\UrlBuilder::setDomain()
     * @covers \CwsOps\LivePerson\Rest\UrlBuilder::create()
     * @covers \CwsOps\LivePerson\Rest\UrlBuilder::addActionContext()
     *
     *
     * @throws BuilderLockedException
     */
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


        try {
            $url->create();
        } catch (\Exception $e) {
            $this->assertInstanceOf(BuilderLockedException::class, $e);
            $this->assertEquals('The URLBuilder is currently locked', $e->getMessage());
        }

        try {
            $url->setDomain('en.liveperson.net');
        } catch (\Exception $e) {
            $this->assertInstanceOf(BuilderLockedException::class, $e);
            $this->assertEquals('The URLBuilder is currently locked', $e->getMessage());
        }


        try {
            $url->setAction('en.liveperson.net');
        } catch (\Exception $e) {
            $this->assertInstanceOf(BuilderLockedException::class, $e);
            $this->assertEquals('The URLBuilder is currently locked', $e->getMessage());
        }

        try {
            $url->setService('en.liveperson.net');
        } catch (\Exception $e) {
            $this->assertInstanceOf(BuilderLockedException::class, $e);
            $this->assertEquals('The URLBuilder is currently locked', $e->getMessage());
        }

        try {
            $url->setAccount('en.liveperson.net');
        } catch (\Exception $e) {
            $this->assertInstanceOf(BuilderLockedException::class, $e);
            $this->assertEquals('The URLBuilder is currently locked', $e->getMessage());
        }

        try {
            $url->addActionContext('en.liveperson.net');
        } catch (\Exception $e) {
            $this->assertInstanceOf(BuilderLockedException::class, $e);
            $this->assertEquals('The URLBuilder is currently locked', $e->getMessage());
        }

        try {
            $url->hasQueryParam(true);
        } catch (\Exception $e) {
            $this->assertInstanceOf(BuilderLockedException::class, $e);
            $this->assertEquals('The URLBuilder is currently locked', $e->getMessage());
        }

        try {
            $url->addQueryParam('foo', 'bar');
        } catch (\Exception $e) {
            $this->assertInstanceOf(BuilderLockedException::class, $e);
            $this->assertEquals('The URLBuilder is currently locked', $e->getMessage());
        }
        try {
            $url->setVersion('2912');
        } catch (\Exception $e) {
            $this->assertInstanceOf(BuilderLockedException::class, $e);
            $this->assertEquals('The URLBuilder is currently locked', $e->getMessage());
        }
    }

    /**
     * @covers \CwsOps\LivePerson\Rest\URLNotBuiltException
     * @covers \CwsOps\LivePerson\Rest\UrlBuilder::getUrl()
     *
     */
    public function testThrowsUrlNotBuiltException()
    {
        $builder = new UrlBuilder();

        try {
            $builder->getUrl();
        } catch (\Exception $e) {
            $this->assertInstanceOf(URLNotBuiltException::class, $e);
            $this->assertEquals('the URL has not been built, you need to call UrlBuilder::build() before getting the URL', $e->getMessage());
        }
    }
}
