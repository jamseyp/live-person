[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jamseyp/live-person/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jamseyp/live-person/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/jamseyp/live-person/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/jamseyp/live-person/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/jamseyp/live-person/badges/build.png?b=master)](https://scrutinizer-ci.com/g/jamseyp/live-person/build-status/master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/jamseyp/live-person/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)

Live Person PHP
A simple PHP library for connecting to the LivePerson Api.

https://developers.liveperson.com



Usage

```php
use CwsOps\LivePerson\Account\Config;
use CwsOps\LivePerson\Engage;

$config = new Config($accountId,$consumerKey,$consumerSecret,$token,$tokenSecret,$username);

$livePerson = new Engage($config);

$currentAgentStatus = $livePerson->getAgentStatus();

```