<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 12/07/2018
 * Time: 15:48
 */

namespace CwsOps\LivePerson\Rest;

/**
 *  Class Url
 *
 *  Handles the creation of a LivePerson API URL.
 *
 *  This follows a fluid interface for ease of use.
 *
 * @package CwsOps\LivePerson
 */
class UrlBuilder
{
    /** @var string $url the built url */
    private $url;
    /** @var bool true or false if the URL has a query */
    private $hasQuery = false;
    /** @var bool true or false if the builder is currently locked. */
    private $locked = false;
    /** @var bool $isSecure true or false if the URL should be secure. */
    private $isSecure = true;
    /** @var string $domain */
    private $domain;
    /** @var string $service */
    private $service;
    /** @var string $action */
    private $action;
    /** @var string $account */
    private $account;
    /** @var array $queryParams */
    private $queryParams = [];
    /** @var int the version. */
    private $version = 1;
    /** @var string|null any additional context to the action. */
    private $actionContext = null;


    /**
     * Starts creating URL
     *
     * @param bool $secure true or false if the Url should be secure.
     *
     * @return UrlBuilder
     *
     * @throws BuilderLockedException
     */
    public function create(bool $secure = true): UrlBuilder
    {
        if (!$this->isValid()) {
            throw new BuilderLockedException();
        }
        $this->isSecure = $secure;

        return $this;
    }

    /**
     * Sets the domain.
     *
     * @param string $domain
     *
     * @return UrlBuilder
     *
     * @throws BuilderLockedException
     */
    public function setDomain(string $domain): UrlBuilder
    {
        if (!$this->isValid()) {
            throw new BuilderLockedException();
        }
        $this->domain = $domain;

        return $this;
    }

    /**
     * Sets the service
     *
     * @param string $service
     *
     * @return UrlBuilder
     *
     * @throws BuilderLockedException
     */
    public function setService(string $service): UrlBuilder
    {
        if (!$this->isValid()) {
            throw new BuilderLockedException();
        }

        $this->service = $service;

        return $this;
    }

    /**
     * @param $context
     *
     * @return UrlBuilder
     *
     * @throws BuilderLockedException
     */
    public function addActionContext($context): UrlBuilder
    {
        if (!$this->isValid()) {
            throw new BuilderLockedException();
        }

        $this->actionContext = $context;

        return $this;
    }

    /**
     * Sets the action
     *
     * @param string $action
     *
     * @return UrlBuilder
     *
     * @throws BuilderLockedException
     */
    public function setAction(string $action): UrlBuilder
    {
        if (!$this->isValid()) {
            throw new BuilderLockedException();
        }

        $this->action = $action;

        return $this;
    }

    /**
     * Sets the account.
     *
     * @param string $account
     *
     * @return UrlBuilder
     *
     * @throws BuilderLockedException
     */
    public function setAccount(string $account): UrlBuilder
    {
        if (!$this->isValid()) {
            throw new BuilderLockedException();
        }

        $this->account = $account;

        return $this;
    }

    /**
     * Sets the URL. to have a query parameters.
     *
     * @param bool $hasParam
     *
     * @return UrlBuilder
     */
    public function hasQueryParam(bool $hasParam): UrlBuilder
    {
        $this->hasQuery = $hasParam;

        return $this;
    }


    /**
     * Adds any Query parameters
     *
     * @param string $key a key to place into the url
     * @param string $value the value relating to the key.
     *
     * @return UrlBuilder
     *
     * @throws BuilderLockedException
     */
    public function addQueryParam(string $key, string $value): UrlBuilder
    {
        if (!$this->isValid()) {
            return $this;
        }

        if (!$this->hasQuery) {
            return $this;
        }

        if (null !== $value) {
            $this->queryParams[$key] = $value;
        }

        return $this;
    }

    /**
     * Sets the version.
     *
     * @param string $version
     *
     * @return UrlBuilder
     *
     * @throws BuilderLockedException
     */
    public function setVersion(string $version): UrlBuilder
    {
        if (!$this->isValid()) {
            throw new BuilderLockedException();
        }
        $this->version = $version;
        return $this;
    }

    /**
     * Builds the URL from the parameters passed.
     *
     * @return UrlBuilder
     */
    public function build(): UrlBuilder
    {
        $this->createUrl();
        $this->locked = true;
        return $this;
    }

    /**
     * Gets the built URL.
     *
     * @return string
     *
     * @throws URLNotBuiltException
     */
    public function getUrl(): string
    {
        if (!$this->locked) {
            throw new URLNotBuiltException();
        }

        $generatedUrl = $this->url;
        $this->url = null;
        $this->locked = false;

        return $generatedUrl;
    }

    /**
     * Returns true or false if the URL is built.
     *
     * @return bool
     */
    public function isUrlBuilt()
    {
        return $this->locked;
    }

    /**
     * Creates the URL from all the different parts.
     *
     * @return void
     */
    private function createUrl()
    {
        // Build the URL.
        $url = $this->isSecure ? $url = 'https://' : $url = 'http://';

        // Add the attributes, to the URL.
        $this->addToUrl($url, $this->domain);
        $this->addToUrl($url, $this->service);
        $this->addToUrl($url, 'api/account');
        $this->addToUrl($url, $this->account);
        $this->addToUrl($url, $this->action);
        $this->addToUrl($url, $this->actionContext, false);

        // If the query has any parameters add them now.
        if ($this->hasQuery) {
            $url .= '?';
            foreach ($this->queryParams as $key => $value) {
                $url .= $key . '=' . $value;
            }

            $url .= 'v=' . $this->version;
        } else {
            $url .= '?v=' . $this->version;
        }

        $this->url = $url;
    }

    /**
     * Adds an attribute
     *
     * Note: the url is passed by reference.
     *
     * @param string $url The url to edit.
     * @param string $attribute the attribute to add.
     * @param bool $trailingSlash true or false to add the trailing slash.
     *
     * @return void
     */
    private function addToUrl(&$url, $attribute, $trailingSlash = true)
    {
        if (null !== $attribute) {
            $url .= $attribute;

            if ($trailingSlash) {
                $url .= '/';
            }
        }
    }

    /**
     */
    private function isValid()
    {
        if (!$this->locked) {
            return true;
        }
        return false;
    }
}
