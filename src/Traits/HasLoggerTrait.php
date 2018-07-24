<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 20/07/2018
 * Time: 11:14
 */

namespace CwsOps\LivePerson\Traits;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @codeCoverageIgnore
 * Trait HasLoggerTrait
 *
 * @package CwsOps\LivePerson\Traits
 */
trait HasLoggerTrait
{
    /**
     * Quick method to check if a logger has been passed if not it will return a PSR NullLogger
     * @param LoggerInterface|null $logger
     *
     * @return LoggerInterface
     */
    protected function hasLogger(LoggerInterface $logger = null): LoggerInterface
    {
        if (null === $logger) {
            return new NullLogger();
        }
        return $logger;
    }
}
