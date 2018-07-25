<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 25/07/2018
 * Time: 08:43
 */

namespace CwsOps\LivePerson\Services;

use CwsOps\LivePerson\Rest\Request;

/**
 * Class OperationalRTService
 * @see https://developers.liveperson.com/data-operational-realtime-overview.html
 * @package CwsOps\LivePerson\Services
 * @author James Parker <jamseyp@gmail.com>
 */
class OperationalRTService extends AbstractService
{

    const GLUE_CHAR = ',';

    /**
     * Gets the queue heath metrics at account or skill level.
     * @see https://developers.liveperson.com/data-operational-realtime-queue-health.html
     *
     * @param int $timeFrame The time range in minutes max value is 1440 minutes (24 hours)
     * @param array $skillIds A list of skills to filter the data by or null for account level
     * @param int|null $interval the intervals to get data at.
     *
     * @throws \CwsOps\LivePerson\Rest\BuilderLockedException
     * @throws \CwsOps\LivePerson\Rest\URLNotBuiltException
     */
    public function queueHealth(int $timeFrame = 60, array $skillIds = [], int $interval = null)
    {
        if (!$this->isTimeFrameValid($timeFrame)) {
            throw new \InvalidArgumentException(sprintf('The $timeframe must be between 0 and 1440, you passed %d', $timeFrame));
        }
        if (!$this->isIntervalValid($interval)) {
            throw new \InvalidArgumentException(sprintf('The $interval you passed was not valid or not dividable by the $timeframe (%d), you passed %d', $timeFrame, $interval));
        }


        $this->urlBuilder = $this->request->buildUrl($this->getService())
            ->setService('operations')
            ->setAccount($this->config->getAccountId())
            ->setAction('queuehealth')
            ->hasQueryParam(true)
            ->addQueryParam('timeframe', $timeFrame);

        if ([] === $skillIds) {
            // Implode the array into a comma separated string and add to the url.
            $this->urlBuilder->addQueryParam('skillIds', rtrim(implode(self::GLUE_CHAR, $skillIds), self::GLUE_CHAR));
        }

        if (null !== $interval) {
            $this->urlBuilder->addQueryParam('interval', $interval);
        }

        $this->urlBuilder->build()->getUrl();

        $this->handle($payload = [], Request::METHOD_GET);
    }

    /**
     * Retrieves engagement activity-related metrics at the account, skill, or agent level
     * @see https://developers.liveperson.com/data-operational-realtime-engagement-activity.html
     *
     * @param int $timeFrame The time range in minutes max value is 1440 minutes (24 hours)
     * @param array $agents an array of agents to get data for, this can be left empty to return data for all agents
     * @param array $skillIds an array of skills to filter by, this can be left empty to get data for all skills.
     * @param int|null $interval the interval to filter at.
     *
     * @throws \CwsOps\LivePerson\Rest\BuilderLockedException
     * @throws \CwsOps\LivePerson\Rest\URLNotBuiltException
     */
    public function engagementActivity(int $timeFrame = 60, array $agents = [], array $skillIds = [], int $interval = null)
    {
        if (!$this->isTimeFrameValid($timeFrame)) {
            $message = sprintf('The $timeframe must be between 0 and 1440, you passed %d', $timeFrame);
            throw new \InvalidArgumentException($message);
        }
        if (!$this->isIntervalValid($interval)) {
            $message = sprintf('The $interval you passed was not valid or not dividable by the $timeframe (%d), you passed %d', $timeFrame, $interval);
            throw new \InvalidArgumentException($message);
        }

        $this->urlBuilder = $this->request->buildUrl($this->getService())
            ->setService('operations')
            ->setAccount($this->config->getAccountId())
            ->setAccount('engactivity')
            ->hasQueryParam(true)
            ->addQueryParam('timeframe', $timeFrame);

        if (0 !== count($agents)) {
            $this->urlBuilder->addQueryParam('agentIds', rtrim(implode(self::GLUE_CHAR, $agents), self::GLUE_CHAR));
        }
        if (0 !== count($skillIds)) {
            $this->urlBuilder->addQueryParam('skillIds', rtrim(implode(self::GLUE_CHAR, $skillIds), self::GLUE_CHAR));
        }
        if (null !== $interval) {
            $this->urlBuilder->addQueryParam('interval', $interval);
        }

        $payload = [];

        $this->urlBuilder->build()->getUrl();

        $this->handle($payload);
    }

    /**
     * Retrieves Agent State Distribution data
     * @see https://developers.liveperson.com/data-operational-realtime-agent-activity.html
     *
     * @param int $timeFrame The time range in minutes max value is 1440 minutes (24 hours)
     * @param array $agents an array of agents to get data for, this can be left empty to return data for all agents
     * @param int|null $interval $interval the interval to filter at.
     *
     * @throws \CwsOps\LivePerson\Rest\BuilderLockedException
     * @throws \CwsOps\LivePerson\Rest\URLNotBuiltException
     */
    public function agentActivity(int $timeFrame = 60, array $agents = [], int $interval = null)
    {
        if (!$this->isTimeFrameValid($timeFrame)) {
            throw new \InvalidArgumentException(sprintf('The $timeframe must be between 0 and 1440, you passed %d', $timeFrame));
        }
        if (!$this->isIntervalValid($interval)) {
            throw new \InvalidArgumentException(sprintf('The $interval you passed was not valid or not dividable by the $timeframe (%d), you passed %d', $timeFrame, $interval));
        }

        $this->urlBuilder = $this->request->buildUrl($this->getService())
            ->setService('operations')
            ->setAccount($this->config->getAccountId())
            ->setAction('agentactivity');

        // the API requests that if there is a long list of AgentsId, these should be passed in a POST request.
        // So  we check here if the agent list is more than 3 and if so we will instead send a post request.
        $payLoad = [];

        if (count($agents) < 3) {
            $method = Request::METHOD_GET;
            $this->urlBuilder
                ->hasQueryParam(true)
                ->addQueryParam('timeframe', $timeFrame);

            if (0 !== count($agents)) {
                $this->urlBuilder->addQueryParam('agentIds', rtrim(implode(self::GLUE_CHAR, $agents), self::GLUE_CHAR));
            } else {
                $this->urlBuilder->addQueryParam('agentsIds', 'all');
            }

            if (null !== $interval) {
                $this->urlBuilder->addQueryParam('interval', $interval);
            }
        } else {
            $method = Request::METHOD_POST;
            // agents is more than 3, so were going to send a POST request.
            $payLoad['timeframe'] = $timeFrame;
            $payLoad['agentsIds'] = rtrim(implode(self::GLUE_CHAR, $agents), self::GLUE_CHAR);
            if (null !== $interval) {
                $payLoad['interval'] = $interval;
            }
        }

        $this->urlBuilder->build()->getUrl();

        $this->handle($payLoad, $method);
    }


    /**
     * Gets the current Queue State
     *
     * @see https://developers.liveperson.com/data-operational-realtime-current-queue-state.html
     *
     * @param array $skillIds an optional array of skills to filter the queue on.
     *
     * @throws \CwsOps\LivePerson\Rest\BuilderLockedException
     * @throws \CwsOps\LivePerson\Rest\URLNotBuiltException
     */
    public function currentQueueState(array $skillIds = [])
    {
        $this->urlBuilder = $this->request->buildUrl($this->getService())
            ->setAccount($this->config->getAccountId())
            ->setAction('queuestate');

        if (0 !== count($skillIds)) {
            $this->urlBuilder->hasQueryParam(true);
            $this->urlBuilder->addQueryParam('skillIds', rtrim(implode(self::GLUE_CHAR, $skillIds), self::GLUE_CHAR));
        }

        $this->urlBuilder->build()->getUrl();

        $this->handle();
    }


    /**
     * Should provide the Live person service, this service will query against
     *
     * @return string
     */
    protected function getService(): string
    {
        return 'leDataReporting';
    }

    /**
     * Checks if the timeframe is valid.
     * i.e. the time is only valid between 0 and 1440 minutes (0 hrs to 24hrs).
     *
     * @param int $timeFrame the number of minutes.
     *
     * @return bool true or false if the timeframe is valid.
     */
    private function isTimeFrameValid(int $timeFrame): bool
    {
        if ($timeFrame >= 0 && $timeFrame <= 1440) {
            return true;
        }
        return false;
    }

    /**
     * Checks if the interval is valid.
     * The interval must be less than the time frame and must be dividable by the timeframe.
     *
     * @param int $timeFrame the timeframe value.
     * @param int|null $interval the interval to check.
     *
     * @return bool true or false if the interval is valid.
     */
    private function isIntervalValid(int $timeFrame, int $interval = null): bool
    {
        if (null === $interval || $timeFrame < $interval && $interval % $timeFrame === 0) {
            return true;
        }
        return false;
    }
}
