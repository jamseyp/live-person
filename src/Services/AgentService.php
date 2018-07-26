<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 26/07/2018
 * Time: 14:55
 */

namespace CwsOps\LivePerson\Services;

use CwsOps\LivePerson\Rest\Request;

/**
 * Class AgentService
 *
 * @package CwsOps\LivePerson\Services
 * @author James Parker <jamseyp@gmail.com>
 */
class AgentService extends AbstractService
{

    /**
     * @throws \CwsOps\LivePerson\Rest\BuilderLockedException
     */
    public function agentStatus()
    {
        $this->urlBuilder = $this->request->buildUrl($this->getDomain())
            ->setService($this->getService())
            ->setAccount($this->config->getAccountId())
            ->setAction('agent-view')
            ->addActionContext('status');

        $payload = [
            'Status' => ['ONLINE', 'AWAY', 'BACK_SOON', 'OFFLINE'],
            'agentGroupIds' => ['992149632']

        ];

        $this->urlBuilder->build();

        $this->handle($payload, Request::METHOD_POST);
    }

    /**
     * @throws \CwsOps\LivePerson\Rest\BuilderLockedException
     */
    public function agentSummary()
    {
        $this->urlBuilder = $this->request->buildUrl($this->getDomain())
            ->setService($this->getService())
            ->setAccount($this->config->getAccountId())
            ->setAction('agent-view')
            ->addActionContext('summary');

        $payload = [
            'agentGroupIds' => ['992149632']
        ];

        $this->urlBuilder->build();

        $this->handle($payload, Request::METHOD_POST);
    }

    /**
     * Should provide the Live person domain id, this service will query against
     *
     * @return string
     */
    protected function getDomain(): string
    {
        return 'msgHist';
    }

    /**
     * Should provide the Live Person service the service will query against.
     *
     * @return string
     */
    protected function getService(): string
    {
        return 'messaging_history';
    }
}