

###Live Person PHP
A simple PHP library for connecting to the LivePerson Api.



##Usage

```php
use CwsOps\LivePerson\Account\Config;
use CwsOps\LivePerson\Engage;

$config = new Config($accountId,$consumerKey,$consumerSecret,$token,$tokenSecret,$username);

$livePerson = new Engage($config);

$currentAgentStatus = $livePerson->getAgentStatus();

```