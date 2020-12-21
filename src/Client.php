<?php

declare(strict_types=1);

namespace Constellix\Client;

use Constellix\Client\Exceptions\Client\Http\AuthenticationException;
use Constellix\Client\Exceptions\Client\Http\BadRequestException;
use Constellix\Client\Exceptions\Client\Http\HttpException;
use Constellix\Client\Exceptions\Client\Http\NotFoundException;
use Constellix\Client\Exceptions\Client\JsonDecodeException;
use Constellix\Client\Exceptions\Client\ManagerNotFoundException;
use Constellix\Client\Interfaces\ClientInterface;
use Constellix\Client\Interfaces\Managers\AbstractManagerInterface;
use Constellix\Client\Interfaces\Managers\ContactListManagerInterface;
use Constellix\Client\Interfaces\Managers\DomainManagerInterface;
use Constellix\Client\Interfaces\Managers\GeoProximityManagerInterface;
use Constellix\Client\Interfaces\Managers\IPFilterManagerInterface;
use Constellix\Client\Interfaces\Managers\PoolManagerInterface;
use Constellix\Client\Interfaces\Managers\TagManagerInterface;
use Constellix\Client\Interfaces\Managers\TemplateManagerInterface;
use Constellix\Client\Interfaces\Managers\VanityNameserverManagerInterface;
use Constellix\Client\Interfaces\PaginatorFactoryInterface;
use Constellix\Client\Managers\ContactListManager;
use Constellix\Client\Managers\DomainManager;
use Constellix\Client\Managers\GeoProximityManager;
use Constellix\Client\Managers\IPFilterManager;
use Constellix\Client\Managers\PoolManager;
use Constellix\Client\Managers\TagManager;
use Constellix\Client\Managers\TemplateManager;
use Constellix\Client\Managers\VanityNameserverManager;
use Constellix\Client\Pagination\Factories\PaginatorFactory;
use DateTime;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Constellix DNS API Client SDK
 * @package Constellix
 *
 * @property-read TagManagerInterface $tags
 * @property-read ContactListManagerInterface $contactlists;
 * @property-read VanityNameserverManagerInterface $vanitynameservers;
 * @property-read GeoProximityManagerInterface $geoproximity;
 * @property-read IPFilterManagerInterface $ipfilters;
 * @property-read PoolManagerInterface $pools;
 * @property-read TemplateManagerInterface $templates;
 * @property-read DomainManagerInterface $domains;
 */
class Client implements ClientInterface, LoggerAwareInterface
{
    /**
     * The HTTP Client for all requests.
     * @var HttpClientInterface
     */
    protected HttpClientInterface $client;

    /**
     * The Constellix API Key
     * @var string
     */
    protected string $apiKey;

    /**
     * The Constellix Secret Key
     * @var string
     */
    protected string $secretKey;

    /**
     * The Constellix API Endpoint
     * @var string
     */
    protected string $endpoint = 'https://api.constellix.com/v4';

    /**
     * The pagination factory to use for paginated resource collections
     * @var PaginatorFactoryInterface|PaginatorFactory
     */
    protected PaginatorFactoryInterface $paginatorFactory;

    /**
     * Logger interface to use for log messages
     * @var LoggerInterface|NullLogger|null
     */
    public LoggerInterface $logger;

    /**
     * A cache of instantiated manager classes.
     * @var array
     */
    protected array $managers = [];

    /**
     * A map of manager names to classes.
     * @var array|string[]
     */
    protected array $managerMap = [
        'tags' => TagManager::class,
        'contactlists' => ContactListManager::class,
        'vanitynameservers' => VanityNameserverManager::class,
        'geoproximity' => GeoProximityManager::class,
        'ipfilters' => IPFilterManager::class,
        'pools' => PoolManager::class,
        'templates' => TemplateManager::class,
        'domains' => DomainManager::class,
    ];

    /**
     * The request limit on the API.
     * @var int|null
     */
    protected ?int $requestLimit;

    /**
     * The number of requests remaining until the limit is hit.
     * @var int|null
     */
    protected ?int $requestsRemaining;

    /**
     * The time that the request limit resets
     * @var DateTime|null
     */
    protected ?DateTime $limitReset;

    /**
     * Creates a new client.
     *
     * @param HttpClientInterface|null $client
     * @param PaginatorFactoryInterface|null $paginatorFactory
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        ?HttpClientInterface $client = null,
        ?PaginatorFactoryInterface $paginatorFactory = null,
        ?LoggerInterface $logger = null
    ) {
        // If we weren't given a HTTP client, create a new Guzzle client.
        if ($client === null) {
            $client = new \GuzzleHttp\Client;
        }

        // If we don't have a paginator factory, use our own.
        if ($paginatorFactory === null) {
            $this->paginatorFactory = new PaginatorFactory;
        }

        $this->setHttpClient($client);

        // If we don't have a logger, use the null logger.
        if ($logger === null) {
            $logger = new NullLogger();
        }
        $this->logger = $logger;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function setHttpClient(HttpClientInterface $client): self
    {
        $this->client = $client;
        return $this;
    }

    public function getHttpClient(): HttpClientInterface
    {
        return $this->client;
    }

    public function setEndpoint(string $endpoint): self
    {
        $this->endpoint = $endpoint;
        return $this;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function setApiKey(string $key): self
    {
        $this->apiKey = $key;
        return $this;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function setSecretKey(string $key): self
    {
        $this->secretKey = $key;
        return $this;
    }

    public function getSecretKey(): string
    {
        return $this->secretKey;
    }

    public function setPaginatorFactory(PaginatorFactoryInterface $factory): self
    {
        $this->paginatorFactory = $factory;
        return $this;
    }

    public function getPaginatorFactory(): PaginatorFactoryInterface
    {
        return $this->paginatorFactory;
    }

    public function get(string $url, array $params = []): ?object
    {
        $queryString = '';
        if ($params) {
            $queryString = '?' . http_build_query($params);
        }
        $url .= $queryString;

        $request = new Request('GET', $this->endpoint . $url);
        return $this->send($request);
    }

    public function post(string $url, $payload = null): ?object
    {
        $request = new Request('POST', $this->endpoint . $url, [], 'php://temp');
        if ($payload !== null) {
            $request = $request->withHeader('Content-Type', 'application/json');
            $request->getBody()->write(json_encode($payload));
        }
        return $this->send($request);
    }

    public function put(string $url, $payload = null): ?object
    {
        $request = new Request('PUT', $this->endpoint . $url, [], 'php://temp');
        if ($payload !== null) {
            $request = $request->withHeader('Content-Type', 'application/json');
            $request->getBody()->write(json_encode($payload));
        }
        return $this->send($request);
    }

    public function delete(string $url, $payload = null): ?object
    {
        $request = new Request('DELETE', $this->endpoint . $url);
        if ($payload) {
            $request = $request->withHeader('Content-Type', 'application/json');
            $request->getBody()->write(json_encode($payload));
        }
        return $this->send($request);
    }

    public function send(RequestInterface $request): ?object
    {
        $this->logger->debug("[Constellix] API Request: {$request->getMethod()} {$request->getUri()}");

        $request = $request->withHeader('Accept', 'application/json');
        $request = $this->addAuthHeaders($request);
        $response = $this->client->sendRequest($request);

        $this->logger->debug("[Constellix] API Response: {$response->getStatusCode()} {$response->getReasonPhrase()}");
        $this->updateLimits($response);
        $statusCode = $response->getStatusCode();
        if ((int)substr((string)$statusCode, 0, 1) <= 3) {
            $body = json_decode((string) $response->getBody());
            if ($body === false) {
                throw new JsonDecodeException('Failed to decode request');
            }
            return $body;
        } else {
            $lookup = [
                400 => BadRequestException::class,
                401 => AuthenticationException::class,
                404 => NotFoundException::class,
            ];
            if (array_key_exists($statusCode, $lookup)) {
                $exceptionClass = $lookup[$statusCode];
            } else {
                $exceptionClass = HttpException::class;
            }
            $exception = new $exceptionClass($response->getReasonPhrase(), $statusCode);
            $exception->setRequest($request);
            $exception->setResponse($response);
            throw $exception;
        }
    }

    /**
     * Fetch the API request details from the last API response.
     * @param ResponseInterface $response
     */
    protected function updateLimits(ResponseInterface $response)
    {
        $this->requestsRemaining = (int)current($response->getHeader('X-RateLimit-Remaining'));
        if ($this->requestsRemaining === false) {
            $this->requestsRemaining = null;
        }

        $this->requestLimit = (int)current($response->getHeader('X-RateLimit-Limit'));
        if ($this->requestLimit === false) {
            $this->requestLimit = null;
        }

        $reset = (int)current($response->getHeader('X-RateLimit-Reset'));
        $this->limitReset = new DateTime('@' . (time() + $reset));
    }

    /**
     * Get the request limit.
     * @return int|null
     */
    public function getRequestLimit(): ?int
    {
        return $this->requestLimit;
    }

    /**
     * Get the number of requests remaining before you hit the request limit.
     * @return int|null
     */
    public function getRequestsRemaining(): ?int
    {
        return $this->requestsRemaining;
    }

    /**
     * Get the datetime that the request limit resets.
     * @return DateTime|null
     */
    public function getLimitReset(): ?DateTime
    {
        return $this->limitReset;
    }

    /**
     * Adds auth headers to requests. These are generated based on the Api Key and the Secret Key.
     * @param RequestInterface $request
     * @return RequestInterface
     * @throws \Exception
     */
    protected function addAuthHeaders(RequestInterface $request): RequestInterface
    {
        $now = new DateTime('now', new \DateTimeZone('UTC'));
        $timestamp = (string) ($now->getTimestamp() * 1000);
        $hmac = base64_encode(hash_hmac('sha1', $timestamp, $this->getSecretKey(), true));

        $request = $request->withHeader('Authorization', "Bearer {$this->getApiKey()}:{$hmac}:{$timestamp}");
        return $request;
    }

    /**
     * Check if a manager exists with that name in our manager map.
     * @param $name
     * @return bool
     */
    protected function hasManager($name): bool
    {
        $name = strtolower($name);
        return array_key_exists($name, $this->managerMap);
    }

    /**
     * Gets the manager with the specified name.
     * @param $name
     * @return AbstractManagerInterface
     * @throws ManagerNotFoundException
     */
    protected function getManager($name): AbstractManagerInterface
    {
        if (!$this->hasManager($name)) {
            throw new ManagerNotFoundException;
        }

        $name = strtolower($name);

        if (!isset($this->managers[$name])) {
            $this->managers[$name] = new $this->managerMap[$name]($this);
        }

        return $this->managers[$name];
    }

    public function __get($name)
    {
        // If we have a manager with this name, return it.
        if ($this->hasManager($name)) {
            return $this->getManager($name);
        }
    }
}