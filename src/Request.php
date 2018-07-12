<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 12/07/2018
 * Time: 13:35
 */

namespace CwsOps\LivePerson;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class Request
 *
 * @package CwsOps\LivePerson
 */
class Request
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    /**
     * An array of config settings.
     *
     * @var array $config
     */
    private $config;

    /**
     * @var int $retryLimit
     */
    private $retryLimit;

    /**
     * @var LoggerInterface $logger
     */
    private $logger;

    /**
     * @var int retry_counter
     */
    private $retryCounter;


    /**
     * Request constructor.
     *
     * @param array $config the configuration parameters.
     * @param int $retryLimit the number of times to retry on failure.
     * @param LoggerInterface|null $logger
     */
    public function __construct(array $config, int $retryLimit = 3, LoggerInterface $logger = null)
    {
        $this->config = $config;
        $this->retryLimit = $retryLimit;
        $this->logger = $logger ?: new NullLogger();

        // Set the retry counter to zero.
        $this->retryCounter = 0;
    }

    /**
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
        $client = new Client();

        $args = [
            'auth' => 'oauth',
            'headers' => [
                'content-type' => 'application/json',
            ],
            'body' => $payload ?: '{}'
        ];

        try {
            $response = $client->request($method, $url, $args);
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

}