<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 19/07/2018
 * Time: 12:17
 */

namespace CwsOps\LivePerson\Services;

use CwsOps\LivePerson\Account\Config;
use CwsOps\LivePerson\Rest\Request;
use CwsOps\LivePerson\Rest\UrlBuilder;
use CwsOps\LivePerson\Traits\HasLoggerTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Class AbstractService
 *
 * @package CwsOps\LivePerson\Services
 */
abstract class AbstractService
{
    use HasLoggerTrait;

    const REQUEST_TYPE_V1 = 1;
    const REQUEST_TYPE_V2 = 2;

    const GLUE_CHAR = ',';

    /** @var UrlBuilder */
    public $urlBuilder;
    /** @var Config */
    protected $config;
    /** @var LoggerInterface */
    private $logger;
    /** @var int */
    private $retryLimit;
    /** @var Request */
    protected $request;
    /** @var bool $responseSent */
    private $responseSent;
    /** @var \stdClass */
    protected $response;

    /**
     * AbstractService constructor.
     *
     * @param Config $config
     * @param int $retryLimit
     * @param LoggerInterface|null $logger
     */
    public function __construct(Config $config, int $retryLimit = 3, LoggerInterface $logger = null)
    {
        if ($retryLimit > 5) {
            throw new \InvalidArgumentException(
                sprintf('Maximum $retryLimit is 5 you tried setting %d, try setting a value between 0 and 5',
                    $retryLimit)
            );
        }


        $this->config = $config;
        $this->retryLimit = $retryLimit;
        $this->logger = $this->hasLogger($logger);

        $this->request = new Request($config, $retryLimit, $logger);
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }


    /**
     * @codeCoverageIgnore
     * Gets the current status of the account api.
     * @return array|\stdClass
     */
    public function getStatus()
    {
        // @codeCoverageIgnore
        $url = "https://status.liveperson.com/json?site={$this->config->getAccountId()}";

        $response = $this->request->v1($url, Request::METHOD_GET);

        return $response;
    }

    /**
     * Gets the response.
     *
     * @return \stdClass
     *
     * @throws RequestNotSentException
     */
    public function getResponse()
    {
        if (!$this->responseSent) {
            throw new RequestNotSentException();
        }


        return $this->response; //@codeCoverageIgnore
    }

    /**
     * Should provide the Live person domain id, this service will query against
     *
     * @return string
     */
    abstract protected function getDomain(): string;

    /**
     * Should provide the Live Person service the service will query against.
     *
     * @return string
     */
    abstract protected function getService(): string;


    /**
     * @codeCoverageIgnore
     * Handles the request and sets the response property.
     *
     * @param array $data Any data to pass in the request
     * @param string $method the HTTP request type.
     * @param int $type what type of request to make.
     *
     */
    protected function handle($data = [], $method = Request::METHOD_GET, $type = AbstractService::REQUEST_TYPE_V1)
    {
        // Check if the URL was built.
        if (!$this->urlBuilder->isUrlBuilt()) {
            $this->urlBuilder->build();
            $this->logger->debug("The URL was not built when trying to handle the request");
        }

        if ($type === self::REQUEST_TYPE_V1) {
            try {
                $this->response = $this->request->v1($this->urlBuilder->getUrl(), $method, $data);
                $this->responseSent = true;
            } catch (\Exception $exception) {
                $this->logger->error("An exception occurred while the request took place: %s", $exception->getTrace());
            }
        } elseif ($type === self::REQUEST_TYPE_V2) {
            try {
                $this->response = $this->request->v2($this->urlBuilder->getUrl(), $method, $data);
                $this->responseSent = true;
            } catch (\Exception $exception) {
                $this->logger->error("An exception occurred while the request took place: %s", $exception->getTrace());
            }
        }
    }

    /**
     * @codeCoverageIgnore
     *
     * Converts a datetime obj into a int represents milliseconds since the epoc.
     *
     * @param \DateTime $dateTime
     *
     * @return int
     */
    protected function dateTimeToMilliseconds(\DateTime $dateTime)
    {
        return strtotime($dateTime->format('Y-m-d H:i:sP'));
    }

    /**
     * @codeCoverageIgnore
     * Converts a array to a string separated by a glue character.
     *
     * @param array $list the array to separate.
     * @param string $glueChar the character to glue the values together with.
     *
     * @return string the generated string.
     */
    protected function arrayToList(array $list, $glueChar = AbstractService::GLUE_CHAR)
    {
        return rtrim(implode($glueChar, $list), $glueChar);
    }

    /**
     * @codeCoverageIgnore
     * Logs an entry to the logger.
     *
     * @param string $message the message to log.
     * @param string $logLevel the level to log at.
     * @param array $context any additional context.
     */
    protected function log(string $message, string $logLevel = LogLevel::DEBUG, array $context = [])
    {
        $this->logger->log($logLevel, $message, $context);
    }
}
