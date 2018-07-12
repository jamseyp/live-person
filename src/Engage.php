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

    private $account;
    private $skills = [];
    private $historyLimit;
    private $interactive = false;
    private $request;
    private $logger;
    private $ended = false;


    public function __construct(array $config, int $retryLimit = 3, LoggerInterface $logger = null)
    {
        $this->request = new Request([], $retryLimit, $logger);
        $this->historyLimit = 50;
        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * Gets the domain for a specified service
     *
     * @param string $service
     *
     * @return string
     */
    public function domain(string $service)
    {
        $response = $this->request->v1("https://api.liveperson.net/api/account/{$this->account}/service/{$service}/baseURI.json?version=" . self::API_VERSION . "", Request::METHOD_GET);

        return $response->baseUri;
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
        $url = "https://{$this->domain('smt')}/api/account/{$this->account}/monitoring/visitors/{$visitorId}/visits/current/events?v=1&sid={$sessionId}";

        return false === $setData ? $this->request->v1($url, Request::METHOD_GET) : $this->request->v1($url, Request::METHOD_POST, $setData);
    }

    /**
     * @param \DateTime $start
     * @param \DateTime $end
     * @param bool $url
     *
     * @return array|\stdClass
     */
    public function retrieveHistory(\DateTime $start, \DateTime $end, $url = false)
    {
        $url = $url ?: "https://{$this->domain(self::ENGAGEMENT_HISTORY_SERVICE)}/interaction_history/api/account/{$this->account}/interactions/search?limit={$this->historyLimit}&offset=0";

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
     * @param bool $url
     *
     * @return array|\stdClass
     */
    public function retrieveMessageHistory(\DateTime $start, \DateTime $end, $url = false)
    {
        $url = $url ?: "https://{$this->domain(self::MESSAGE_HISTORY_SERVICE)}/messaging_history/api/account/{$this->account}/conversations/search?limit={$this->historyLimit}&offset=0&sort=start:desc";
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
        $url = "https://status.liveperson.com/json?site={$this->account}";

        $response = $this->request->v1($url, Request::METHOD_GET);

        return $response;
    }
}