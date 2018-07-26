<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 26/07/2018
 * Time: 15:57
 */

namespace CwsOps\LivePerson\Services;

/**
 * Class MessagingService
 * @package CwsOps\LivePerson\Services
 * @author James Parker <jamseyp@gmail.com>
 */
class MessagingService extends AbstractService
{

    /**
     * Retrieves engagement activity-related metrics at the account, skill, or agent level
     * @see https://developers.liveperson.com/data-messaging-operations-messaging-conversation.html
     *
     * @param int $timeFrame The time range in minutes max value is 1440 minutes (24 hours)
     * @param array $agents an array of agents to get data for, this can be left empty to return data for all agents
     * @param array $skillIds an array of skills to filter by, this can be left empty to get data for all skills.
     * @param int|null $interval the interval to filter at.
     *
     * @throws \CwsOps\LivePerson\Rest\BuilderLockedException
     */
    public function conversation(int $timeFrame = 60, array $agents = [], array $skillIds = [], int $interval = null)
    {
        $this->urlBuilder = $this->request->buildUrl($this->getDomain())
            ->setService($this->getService())
            ->setAccount($this->config->getAccountId())
            ->setAction('msgconversation')
            ->hasQueryParam(true)
            ->addQueryParam('timeframe', $timeFrame);


        if (0 !== count($agents)) {
            $this->urlBuilder->addQueryParam('agentIds', $this->arrayToList($agents));

        } else {
            $this->urlBuilder->addQueryParam('agentIds', 'all');
        }

        if (0 !== count($skillIds)) {
            $this->urlBuilder->addQueryParam('skillIds', $this->arrayToList($skillIds));
        } else {
            $this->urlBuilder->addQueryParam('skillsIds', 'all');
        }


        if (null !== $interval) {
            $this->urlBuilder->addQueryParam('interval', $interval);
        }

        $payload = [];

        $this->urlBuilder->build();

        $this->handle($payload);
    }


    /**
     * Should provide the Live person domain id, this service will query against
     *
     * @return string
     */
    protected function getDomain(): string
    {
        return 'leDataReporting';
    }

    /**
     * Should provide the Live Person service the service will query against.
     *
     * @return string
     */
    protected function getService(): string
    {
        return 'operations';
    }
}