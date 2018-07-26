<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 12/07/2018
 * Time: 13:35
 */

namespace CwsOps\LivePerson\Rest;

use CwsOps\LivePerson\Account\Config;
use CwsOps\LivePerson\Traits\HasLoggerTrait;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Psr\Log\LoggerInterface;

/**
 *
 * Class Request
 *
 * @package CwsOps\LivePerson
 */
class Request
{
    use HasLoggerTrait;

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    /**
     * An AccountConfig obj
     *
     * @var Config $config
     */
    private $config;

    /**
     * The total retry limit.
     *
     * @var int $retryLimit
     */
    private $retryLimit;

    /**
     * @var LoggerInterface $logger
     */
    private $logger;

    /**
     * The number of times tried.
     *
     * @var int retry_counter
     */
    private $retryCounter;
    /** @var string $bearer */
    private $bearer;
    /** @var UrlBuilder */
    private $urlBuilder;
    /** @var Client */
    private $client;


    /**
     * Request constructor.
     *
     * @param Config $accountConfig
     * @param int $retryLimit the number of times to retry on failure. Recommended is 3.
     * @param LoggerInterface|null $logger
     */
    public function __construct(Config $accountConfig, int $retryLimit = 3, LoggerInterface $logger = null)
    {
        $this->config = $accountConfig;
        $this->retryLimit = $retryLimit;
        $this->logger = $this->hasLogger($logger);

        // Set the retry counter to zero.
        $this->retryCounter = 0;
        $this->urlBuilder = new UrlBuilder();

        $this->requestClient();
    }

    /**
     * @codeCoverageIgnore
     *
     * Gets the domain for a specified service
     *
     * @param string $service
     *
     * @return string
     */
    public function getDomain(string $service)
    {
        $response = $this->v1("https://api.liveperson.net/api/account/{$this->config->getAccountId()}/service/{$service}/baseURI.json?version=1.0", Request::METHOD_GET);

        return $response->baseURI;
    }

    /**
     * @codeCoverageIgnore
     *
     * Creates a URLBuilder instance with the domain allready set.
     *
     * @param $service
     *
     * @return UrlBuilder|null
     */
    public function buildUrl($service)
    {
        try {
            return $this->urlBuilder->create(true)
                ->setDomain(($this->getDomain($service)));
        } catch (BuilderLockedException $e) {
            $this->logger->critical($e->getMessage());
        }
        return $this->urlBuilder;
    }

    /**
     * @codeCoverageIgnore
     * Performs the actual request on the livePerson api.
     *
     * @param string $url the URL to make the request to.
     * @param string $method The method to request the data
     * @param array|null $payload an array of parameters to place into the body of the request.
     *
     * @return array|\stdClass an array that contains the result or an empty array on error.
     */
    public function v1($url, $method = Request::METHOD_GET, $payload = null)
    {


        $args = [
            'auth' => 'oauth',
            'headers' => [
                'content-type' => 'application/json',
            ],
            'json' => $payload ?: new \stdClass()
        ];


        try {


            $response = $this->requestClient()->request($method, $url, $args);
            $responseBody = json_decode($response->getBody());

            return $responseBody;

        } catch (GuzzleException $e) {
            if ($this->retryCounter < $this->retryLimit || $this->retryLimit === -1) {

                $this->logger->info(sprintf('attempt $d failed trying in %d seconds', $this->retryCounter + 1, 15000));

                usleep(15000);
                $this->retryCounter++;
                $responseBody = $this->v1($url, $method, $payload);

                return $responseBody;
            } else {
                $this->logger->critical(sprintf('client error: %s', $e->getMessage()));
                return [];
            }
        }

    }

    private function requestClient()
    {
        $consumer_key = $this->config->getConsumerKey();
        $consumer_secret = $this->config->getConsumerSecret();
        $token = $this->config->getToken();
        $secret = $this->config->getTokenSecret();
        $stack = HandlerStack::create();
        $auth = new Oauth1([
            'consumer_key' => $consumer_key,
            'consumer_secret' => $consumer_secret,
            'token' => $token,
            'token_secret' => $secret,
            'signature_method' => Oauth1::SIGNATURE_METHOD_HMAC,
        ]);
        $stack->push($auth);
        $client = new Client([
            'handler' => $stack,
        ]);


        return $client;
    }

    /**
     * @codeCoverageIgnore
     *
     * Performs the actual request on the livePerson api.
     *
     * @param string $url the URL to make the request to.
     * @param string $method The method to request the data
     * @param array|null $payload an array of parameters to place into the body of the request.
     * @param array $headers
     *
     * @return \stdClass an array that contains the result or an empty array on error.
     */
    public function v2(string $url, $method, $payload = [], $headers = [])
    {
        $this->login();
        $args = [
            'headers' => array_merge([
                'content-type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->bearer
            ], $headers ?: []),
            'form_params' => $payload ?: '{}'
        ];


        try {
            $response = $this->client->request($method, $url, $args);

            return json_decode($response->getBody());
        } catch (GuzzleException $e) {
            $this->logger->critical(sprintf('client error: %s', $e->getMessage()));
            return new \stdClass();
        }
    }

    /**
     * Gets the the Guzzle Client.
     *
     * @return Client
     */
    public function getClient()
    {

        return $this->client;
    }

    /**
     * Sets the Guzzle Client.
     *
     * @param Client $client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }


    /**
     * @codeCoverageIgnore
     * Logs into the API.
     */
    private function login()
    {
        $auth = [
            'username' => $this->config->getUsername(),
            'appKey' => $this->config->getConsumerKey(),
            'secret' => $this->config->getConsumerSecret(),
            'accessToken' => $this->config->getToken(),
            'accsessTokenSecret' => $this->config->getTokenSecret()
        ];

        $url = "https://api/account/{$this->config->getAccountId()}/login?v-1.3";

        $response = $this->v1($url, Request::METHOD_POST, $auth);

        $this->bearer = $response->bearer;
    }
}
