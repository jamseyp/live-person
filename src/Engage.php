<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 12/07/2018
 * Time: 14:30
 */

namespace CwsOps\LivePerson;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class Engage
 */
class Engage
{
    const API_VERSION = 1;

    const ENGAGEMENT_HISTORY_SERVICE = 'engHistDomain';
    const MESSAGE_HISTORY_SERVICE = 'msgHist';

    private $accountConfig;
    private $skills = [];
    private $historyLimit;
    private $interactive = false;
    private $request;
    private $logger;
    private $ended = false;


    /**
     * Engage constructor.
     *
     * @param AccountConfig $accountConfig
     * @param int $retryLimit
     * @param LoggerInterface|null $logger
     */
    public function __construct(AccountConfig $accountConfig, int $retryLimit = 3, LoggerInterface $logger = null)
    {
        $this->accountConfig = $accountConfig;
        $this->request = new Request($accountConfig, $retryLimit, $logger);
        $this->historyLimit = 50;
        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * Gets or sets visitor attribute information.
     *
     * @param string $visitorId the unique visitor id.
     * @param string $sessionId the current session id.
     * @param bool $setData true or false if the information should be set.
     *
     * @return array|\stdClass
     */
    public function visitor($visitorId, $sessionId, $setData = false)
    {
        $url = $this->request->buildUrl('smt')
            ->setAccount($this->accountConfig->getAccountId())
            ->setAction('monitoring/visitors')
            ->addActionContext($visitorId . '/visits/current/events')
            ->hasQueryParam(true)
            ->addQueryParam('sid', $sessionId)
            ->setVersion(1)
            ->build()
            ->getUrl();


        return false === $setData ? $this->request->v1($url, Request::METHOD_GET)
            : $this->request->v1($url, Request::METHOD_POST, $setData);
    }

    /**
     * @param \DateTime $start
     * @param \DateTime $end
     *
     * @return array|\stdClass
     */
    public function retrieveHistory(\DateTime $start, \DateTime $end)
    {
        $url = $this->request->buildUrl(self::ENGAGEMENT_HISTORY_SERVICE)
            ->setService('interaction_history')
            ->setAccount($this->accountConfig->getAccountId())
            ->setAction('interactions/search')
            ->hasQueryParam(true)
            ->addQueryParam('limit', $this->historyLimit)
            ->addQueryParam('offset', 0)
            ->build()
            ->getUrl();

        $payload = [
            'interactive' => $this->interactive,
            'ended' => false,
            'start' => [
                'from' => $this->dateTimeToMilliseconds($start),
                'to' => $this->dateTimeToMilliseconds($end),
            ],
            'skillIds' => $this->skills
        ];

        $result = $this->request->v1($url, Request::METHOD_POST, $payload);
        $result->records = $result->conversationHistoryRecords;
        $result->conversationHistoryRecords = null;

        return $result;
    }

    /**
     * Retrieves the message history.
     *
     * @param \DateTime $start
     * @param \DateTime $end
     *
     * @return array|\stdClass
     */
    public function retrieveMessageHistory(\DateTime $start, \DateTime $end)
    {
        $url = $this->request->buildUrl(self::MESSAGE_HISTORY_SERVICE)
            ->setService('messaging_history')
            ->setAccount($this->accountConfig->getAccountId())
            ->setAction('conversations/search')
            ->build()
            ->getUrl();

        $data = [
            'status' => $this->ended ? ['CLOSE'] : ['OPEN', 'CLOSE'],
            'start' => [
                'from' => $this->dateTimeToMilliseconds($start),
                'to' => $this->dateTimeToMilliseconds($end),
            ],
            'skillIds' => $this->skills
        ];

        $result = $this->request->V1($url, 'POST', $data);
        $result->records = $result->conversationHistoryRecords;
        $result->conversationHistoryRecords = null;

        return $result;
    }

    /**
     * Gets status of agents based on provided Skill IDs.
     *
     * @param array $skills
     *
     * @return array|\stdClass
     */
    public function getAgentStatus(array $skills)
    {
        $url = $this->request->buildUrl(self::MESSAGE_HISTORY_SERVICE)
            ->setService('messaging_history')
            ->setAccount($this->accountConfig->getAccountId())
            ->setAction('/agent-view/status');
        $data = ['skillsIds' => $skills];

        $response = $this->request->v1($url, Request::METHOD_GET, $data);

        return $response;
    }

    /**
     * Gets the current status of the API.
     *
     * @return array|\stdClass
     */
    public function status()
    {
        $url = "https://status.liveperson.com/json?site={$this->accountConfig->getAccountId()}";

        $response = $this->request->v1($url, Request::METHOD_GET);

        return $response;
    }

    /**
     * Converts a datetime obj into a int represents milliseconds since the epoc.
     *
     * @param \DateTime $dateTime
     *
     * @return int
     */
    private function dateTimeToMilliseconds(\DateTime $dateTime)
    {
        return strtotime($dateTime->format('Y-m-d H:i:sP'));
    }
}
