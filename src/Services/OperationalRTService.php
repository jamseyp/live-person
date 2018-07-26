<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 25/07/2018
 * Time: 08:43
 */

namespace CwsOps\LivePerson\Services;

use CwsOps\LivePerson\Rest\Request;
use Psr\Log\LogLevel;

/**
 * Class OperationalRTService
 * @see https://developers.liveperson.com/data-operational-realtime-overview.html
 * @package CwsOps\LivePerson\Services
 * @author James Parker <jamseyp@gmail.com>
 */
class OperationalRTService extends AbstractService
{
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
    public function queueHealth(int $timeFrame = 60, array $skillIds = [], $interval = null)
    {


        if (!$this->isTimeFrameValid($timeFrame)) {
            throw new \InvalidArgumentException(sprintf('The $timeframe must be between 0 and 1440, you passed %d', $timeFrame));
        }
        if (!$this->isIntervalValid($timeFrame, $interval)) {
            throw new \InvalidArgumentException(sprintf('The $interval you passed was not valid or not dividable by the $timeframe (%d), you passed %d', $timeFrame, $interval));
        }


        $this->urlBuilder = $this->request->buildUrl($this->getDomain())
            ->setService($this->getService())
            ->setAccount($this->config->getAccountId())
            ->setAction('queuehealth')
            ->hasQueryParam(true)
            ->addQueryParam('timeframe', $timeFrame);

        if ([] === $skillIds) {
            // Implode the array into a comma separated string and add to the url.
            $this->urlBuilder->addQueryParam('skillIds', $this->arrayToList($skillIds));
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
        if (!$this->isIntervalValid($timeFrame, $interval)) {
            $message = sprintf('The $interval you passed was not valid or not dividable by the $timeframe (%d), you passed %d', $timeFrame, $interval);
            throw new \InvalidArgumentException($message);
        }

        $this->urlBuilder = $this->request->buildUrl($this->getDomain())
            ->setService($this->getService())
            ->setAccount($this->config->getAccountId())
            ->setAction('engactivity')
            ->hasQueryParam(true)
            ->addQueryParam('timeframe', $timeFrame);

        if (0 !== count($agents)) {
            $this->urlBuilder->addQueryParam('agentIds', $this->arrayToList($agents));
        }
        if (0 !== count($skillIds)) {
            $this->urlBuilder->addQueryParam('skillIds', $this->arrayToList($skillIds));
        }
        if (null !== $interval) {
            $this->urlBuilder->addQueryParam('interval', $interval);
        }

        $payload = [];

        $this->urlBuilder->build()->getUrl();

        $this->handle($payload);
    }


    /**
     * Retrieves the distribution of visitors’ wait time in the queue, before an agent replies to their chat.
     * @see https://developers.liveperson.com/data-operational-realtime-sla-histogram.html
     *
     * @param int $timeFrame the timeframe in minutes to filter the data from.
     * @param array $skillIds an optional array of skills to filter the data by.
     * @param array $groupIds an optional array of groups filter agents by.
     * @param array $histogram an array of histogram values to provide. All values must be multiples of 5.
     *
     * @throws \CwsOps\LivePerson\Rest\BuilderLockedException
     * @throws \CwsOps\LivePerson\Rest\URLNotBuiltException
     */
    public function slaHistogram($timeFrame = 60, array $skillIds = [], array $groupIds = [], array $histogram = [])
    {
        if (!$this->isTimeFrameValid($timeFrame)) {
            throw new \InvalidArgumentException(sprintf('The $timeframe must be between 0 and 1440, you passed %d', $timeFrame));
        }

        // Check if the histogram values are multiples of 5.
        if (count($histogram) > 0) {
            foreach ($histogram as $value) {
                if ($value % 5 != 0) {
                    // One or more of the values is not a multiple of 5.
                    $message = sprintf('One or more of your histogram values is not a multiple of 5. You passed %s', $this->arrayToList($histogram));

                    $this->log($message, LogLevel::ERROR, $histogram);

                    throw new \InvalidArgumentException($message);
                }
            }
        }

        $this->urlBuilder = $this->request->buildUrl($this->getDomain())
            ->setService($this->getService())
            ->setAccount($this->config->getAccountId())
            ->setAction('sla')
            ->hasQueryParam(true)
            ->addQueryParam('timeframe', $timeFrame);

        if (0 !== count($skillIds)) {
            $this->urlBuilder->addQueryParam('skillIds', $this->arrayToList($skillIds));
        }
        if (0 !== count($groupIds)) {
            $this->urlBuilder->addQueryParam('groupIds', $this->arrayToList($groupIds));
        }
        if (0 !== count($histogram)) {
            $this->urlBuilder->addQueryParam('histogram', $this->arrayToList($histogram));
        }

        $this->urlBuilder->build()->getUrl();

        $this->handle();
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
    public function agentActivity(int $timeFrame = 60, array $agents = [], $interval = null)
    {
        if (!$this->isTimeFrameValid($timeFrame)) {
            throw new \InvalidArgumentException(sprintf('The $timeframe must be between 0 and 1440, you passed %d', $timeFrame));
        }
        if (!$this->isIntervalValid($timeFrame, $interval)) {
            throw new \InvalidArgumentException(sprintf('The $interval you passed was not valid or not dividable by the $timeframe (%d), you passed %d', $timeFrame, $interval));
        }

        $this->urlBuilder = $this->request->buildUrl($this->getDomain())
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
            $payLoad['agentsIds'] = $this->arrayToList($agents);
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
        $this->urlBuilder = $this->request->buildUrl($this->getDomain())
            ->setService($this->getService())
            ->setAccount($this->config->getAccountId())
            ->setAction('queuestate');

        if (0 !== count($skillIds)) {
            $this->urlBuilder->hasQueryParam(true);
            $this->urlBuilder->addQueryParam('skillIds', $this->arrayToList($skillIds));
        }

        $this->urlBuilder->build()->getUrl();

        $this->handle();
    }


    /**
     * Should provide the Live person service, this service will query against
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
     * @param int $interval the interval to check.
     *
     * @return bool true or false if the interval is valid.
     */
    private function isIntervalValid($timeFrame, $interval): bool
    {
        if (null === $interval || $interval <= $timeFrame && $timeFrame % $interval === 0) {
            return true;
        }
        return false;
    }
}
