<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 20/07/2018
 * Time: 11:38
 */

namespace CwsOps\LivePerson\Services;

/**
 * Class EngagementService
 *
 * @package CwsOps\LivePerson\Services
 */
class EngagementService extends AbstractService
{
    /**
     * Gets the interaction history.
     *
     * @see https://developers.liveperson.com/data-engagement-history-methods.html
     *
     * @param \DateTime $start the start time of the request
     * @param \DateTime $end the end time of the request
     * @param array $skillIds an array of skills
     * @param int $limit the limit to set. default is 50 max is 100
     * @param int $offset the offset of the query.
     *
     * @throws \CwsOps\LivePerson\Rest\BuilderLockedException
     * @throws \CwsOps\LivePerson\Rest\URLNotBuiltException
     */
    public function interactionHistory(\DateTime $start, \DateTime $end, array $skillIds = [], int $limit = 50, int $offset = 0)
    {
        if ($limit < 0 || $limit > 100) {
            throw new \InvalidArgumentException(sprintf('$limit can only be value between 0 and 100'));
        }

        $this->urlBuilder = $this->request->buildUrl($this->getDomain())
            ->setService($this->getService())
            ->setAccount($this->config->getAccountId())
            ->setAction('interactions/search')
            ->hasQueryParam(true)
            ->addQueryParam('limit', $limit)
            ->addQueryParam('offset', $offset)
            ->build()
            ->getUrl();

        $payload = [
            'interactive' => false,
            'ended' => false,
            'start' => [
                'from' => $this->dateTimeToMilliseconds($start),
                'to' => $this->dateTimeToMilliseconds($end),
            ],
            'skillIds' => $skillIds
        ];

        $this->handle($payload);
    }


    /**
     * @codeCoverageIgnore
     *
     * Should provide the Live person service, this service will query against
     *
     * @return string
     */
    protected function getDomain(): string
    {
        return 'engHistDomain';
    }

    /**
     * Should provide the Live Person service the service will query against.
     *
     * @return string
     */
    protected function getService(): string
    {
        return 'interaction_history';
    }
}