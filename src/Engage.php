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
        $url = "https://{$this->request->getDomain('smt')}/api/account/{$this->accountConfig->getAccountId()}/monitoring/visitors/{$visitorId}/visits/current/events?v=1&sid={$sessionId}";

        return false === $setData ? $this->request->v1($url, Request::METHOD_GET) : $this->request->v1($url, Request::METHOD_POST, $setData);
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
                'from' => $start->getTimestamp() . "000",
                'to' => $end->getTimestamp() . "000",
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
                'from' => $start->getTimestamp() . '000',
                'to' => $end->getTimestamp() . '000',
            ],
            'skillIds' => $this->skills
        ];

        $result = $this->request->V1($url, 'POST', $data);
        $result->records = $result->conversationHistoryRecords;
        $result->conversationHistoryRecords = null;

        return $result;
    }

    public function status()
    {
        $url = "https://status.liveperson.com/json?site={$this->accountConfig->getAccountId()}";

        $response = $this->request->v1($url, Request::METHOD_GET);

        return $response;
    }
}