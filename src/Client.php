<?php

declare(strict_types=1);

namespace Constellix\Client;

use Carbon\Carbon;
use Constellix\Client\Exceptions\Client\Http\AuthenticationException;
use Constellix\Client\Exceptions\Client\Http\BadRequestException;
use Constellix\Client\Exceptions\Client\Http\HttpException;
use Constellix\Client\Exceptions\Client\Http\NotFoundException;
use Constellix\Client\Exceptions\Client\JsonDecodeException;
use Constellix\Client\Exceptions\Client\ManagerNotFoundException;
use Constellix\Client\Exceptions\ConstellixException;
use Constellix\Client\Interfaces\ConstellixApiClient;
use Constellix\Client\Interfaces\PaginatorFactoryInterface;
use Constellix\Client\Managers\AbstractManager;
use Constellix\Client\Managers\AnalyticsManager;
use Constellix\Client\Managers\ContactListManager;
use Constellix\Client\Managers\DomainManager;
use Constellix\Client\Managers\GeoProximityManager;
use Constellix\Client\Managers\IPFilterManager;
use Constellix\Client\Managers\PoolManager;
use Constellix\Client\Managers\TagManager;
use Constellix\Client\Managers\TemplateManager;
use Constellix\Client\Managers\VanityNameserverManager;
use Constellix\Client\Pagination\Factories\PaginatorFactory;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @property-read TagManager $tags Manager for Tags
 * @property-read ContactListManager $contactlists Manager for Contact Lists
 * @property-read VanityNameserverManager $vanitynameservers Manager for Vanity Nameservers
 * @property-read GeoProximityManager $geoproximity Manager for Geoproximities
 * @property-read IPFilterManager $ipfilters Manager for IP Filters
 * @property-read PoolManager $pools Manager for Pools
 * @property-read TemplateManager $templates Manager for Templates
 * @property-read DomainManager $domains Manager for Domains
 * @property-read AnalyticsManager $analytics Manager for Account Analytics
 */
class Client implements LoggerAwareInterface, ConstellixApiClient
{
    /**
     * The HTTP Client for all requests.
     * @var null|HttpClientInterface
     */
    protected ?HttpClientInterface $client = null;

    /**
     * The Constellix API Key
     * @var null|string
     */
    protected ?string $apiKey = null;

    /**
     * The Constellix Secret Key
     * @var null|string
     */
    protected ?string $secretKey = null;

    /**
     * The Constellix API Endpoint
     * @var string
     */
    protected string $endpoint = 'https://api.dns.constellix.com/v4';

    /**
     * The pagination factory to use for paginated resource collections
     * @var PaginatorFactoryInterface
     */
    protected PaginatorFactoryInterface $paginatorFactory;

    /**
     * Logger interface to use for log messages
     * @var LoggerInterface|NullLogger
     */
    public LoggerInterface $logger;

    /**
     * A cache of instantiated manager classes.
     * @var array<AbstractManager>
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
        'analytics' => AnalyticsManager::class,
    ];

    /**
     * The request limit on the API.
     * @var int|null
     */
    protected ?int $requestLimit = null;

    /**
     * The number of requests remaining until the limit is hit.
     * @var int|null
     */
    protected ?int $requestsRemaining = null;

    /**
     * The time that the request limit resets
     * @var Carbon|null
     */
    protected ?Carbon $limitReset = null;

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

        // If we don't have a paginator factory, use our own.
        if ($paginatorFactory === null) {
            $paginatorFactory = new PaginatorFactory();
        }
        $this->paginatorFactory = $paginatorFactory;

        if ($client) {
            $this->setHttpClient($client);
        }

        // If we don't have a logger, use the null logger.
        if ($logger === null) {
            $logger = new NullLogger();
        }
        $this->logger = $logger;
    }

    /**
     * Sets the logger to use. The Logger must be PSR-3 compatible.
     * @param LoggerInterface $logger
     * @return void
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * Returns the current logger.
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * Sets the HTTP Client for requests to the API. It must be PSR-18 compatible.
     * @param HttpClientInterface $client
     * @return self
     */
    public function setHttpClient(HttpClientInterface $client): self
    {
        $this->client = $client;
        return $this;
    }

    /**
     * Returns the current HTTP client.
     * @return HttpClientInterface|null
     */
    public function getHttpClient(): ?HttpClientInterface
    {
        return $this->client;
    }

    /**
     * Set the API endpoint for the Constellix DNS v4 API,
     * @param string $endpoint
     * @return self
     */
    public function setEndpoint(string $endpoint): self
    {
        $this->endpoint = $endpoint;
        return $this;
    }

    /**
     * Return the current API endpoint,
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * Set the Constellix API Key.
     * @param string $key
     * @return self
     */
    public function setApiKey(string $key): self
    {
        $this->apiKey = $key;
        return $this;
    }

    /**
     * Fetch the current Constellix API Key.
     * @return string|null
     */
    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    /**
     * Set the Constellix Secret Key.
     * @param string $key
     * @return self
     */
    public function setSecretKey(string $key): self
    {
        $this->secretKey = $key;
        return $this;
    }

    /**
     * Fetch the current Constellix Secret Key.
     * @return string|null
     */
    public function getSecretKey(): ?string
    {
        return $this->secretKey;
    }

    /**
     * Set the pagination factory to use. This factory will be used to construct all paginated results from the managers.
     * @param PaginatorFactoryInterface $factory
     * @return $this
     */
    public function setPaginatorFactory(PaginatorFactoryInterface $factory): self
    {
        $this->paginatorFactory = $factory;
        return $this;
    }

    /**
     * Return the current pagination factory.
     * @return PaginatorFactoryInterface
     */
    public function getPaginatorFactory(): PaginatorFactoryInterface
    {
        return $this->paginatorFactory;
    }

    /**
     * Make a GET request to the Constellix API.
     * @param string $url
     * @param array<mixed> $params
     * @return \stdClass|null
     * @throws HttpException
     * @throws JsonDecodeException
     * @internal
     */
    public function get(string $url, array $params = []): ?\stdClass
    {
        $queryString = '';
        if ($params) {
            $queryString = '?' . http_build_query($params);
        }
        $url .= $queryString;

        $request = new Request('GET', $this->endpoint . $url);
        return $this->send($request);
    }

    /**
     * Make a POST request to the Constellix API.
     * @param string $url
     * @param mixed|null $payload
     * @return \stdClass|null
     * @throws ConstellixException
     * @throws HttpException
     * @throws JsonDecodeException
     * @internal
     */
    public function post(string $url, mixed $payload = null): ?\stdClass
    {
        $request = new Request('POST', $this->endpoint . $url, [], 'php://temp');
        if ($payload !== null) {
            $request = $request->withHeader('Content-Type', 'application/json');
            $jsonPayload = json_encode($payload);
            if ($jsonPayload === false) {
                throw new ConstellixException('Unable to encode API payload');
            }
            $request->getBody()->write($jsonPayload);
        }
        return $this->send($request);
    }

    /**
     * Make a PUT request to the Constellix API.
     * @param string $url
     * @param mixed|null $payload
     * @return \stdClass|null
     * @throws ConstellixException
     * @throws HttpException
     * @throws JsonDecodeException
     * @internal
     */
    public function put(string $url, mixed $payload = null): ?\stdClass
    {
        $request = new Request('PUT', $this->endpoint . $url, [], 'php://temp');
        if ($payload !== null) {
            $request = $request->withHeader('Content-Type', 'application/json');
            $jsonPayload = json_encode($payload);
            if ($jsonPayload === false) {
                throw new ConstellixException('Unable to encode API payload');
            }
            $request->getBody()->write($jsonPayload);
        }
        return $this->send($request);
    }

    /**
     * Make a DELETE request to the Constellix API.
     * @param string $url
     * @param mixed|null $payload
     * @return \stdClass|null
     * @throws ConstellixException
     * @throws HttpException
     * @throws JsonDecodeException
     * @internal
     */
    public function delete(string $url, mixed $payload = null): ?\stdClass
    {
        $request = new Request('DELETE', $this->endpoint . $url);
        if ($payload) {
            $request = $request->withHeader('Content-Type', 'application/json');
            $jsonPayload = json_encode($payload);
            if ($jsonPayload === false) {
                throw new ConstellixException('Unable to encode API payload');
            }
            $request->getBody()->write($jsonPayload);
        }
        return $this->send($request);
    }

    /**
     * Build and send a request to the API.
     * @param RequestInterface $request
     * @return \stdClass|null
     * @throws ConstellixException
     * @throws HttpException
     * @throws JsonDecodeException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @internal
     */
    public function send(RequestInterface $request): ?\stdClass
    {
        if (!$this->client) {
            throw new ConstellixException('No HTTP client has been specified');
        }
        $this->logger->debug("[Constellix] API Request: {$request->getMethod()} {$request->getUri()}");

        $request = $request->withHeader('Accept', 'application/json');
        $request = $this->addAuthHeaders($request);
        $response = $this->client->sendRequest($request);

        $this->logger->debug("[Constellix] API Response: {$response->getStatusCode()} {$response->getReasonPhrase()}");
        $this->updateLimits($response);
        $statusCode = $response->getStatusCode();
        if ((int)substr((string)$statusCode, 0, 1) <= 3) {
            $body = (string)$response->getBody();
            if ($body) {
                try {
                    $body = json_decode(json: $body, flags: JSON_THROW_ON_ERROR);
                } catch (\JsonException) {
                    throw new JsonDecodeException('Failed to decode request');
                }
            } else {
                $body = null;
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
    protected function updateLimits(ResponseInterface $response): void
    {
        $this->requestsRemaining = (int)current($response->getHeader('X-RateLimit-Remaining'));
        $this->requestLimit = (int)current($response->getHeader('X-RateLimit-Limit'));
        $reset = (int)current($response->getHeader('X-RateLimit-Reset'));
        $this->limitReset = Carbon::now()->addSeconds($reset);
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
     * @return Carbon|null
     */
    public function getLimitReset(): ?Carbon
    {
        return $this->limitReset;
    }

    /**
     * Adds auth headers to requests. These are generated based on the Api Key and the Secret Key.
     * @param RequestInterface $request
     * @return RequestInterface
     * @throws \Exception
     * @internal
     */
    protected function addAuthHeaders(RequestInterface $request): RequestInterface
    {
        $now = Carbon::now('UTC');
        $timestamp = (string) ($now->getTimestamp() * 1000);
        $hmac = base64_encode(hash_hmac('sha1', $timestamp, (string)$this->getSecretKey(), true));

        $request = $request->withHeader('Authorization', "Bearer {$this->getApiKey()}:{$hmac}:{$timestamp}");
        return $request;
    }

    /**
     * Check if a manager exists with that name in our manager map.
     * @param string $name
     * @return bool
     */
    protected function hasManager(string $name): bool
    {
        $name = strtolower($name);
        return array_key_exists($name, $this->managerMap);
    }

    /**
     * Gets the manager with the specified name.
     * @param string $name
     * @return AbstractManager
     */
    protected function getManager(string $name): AbstractManager
    {
        $name = strtolower($name);

        if (!isset($this->managers[$name])) {
            /**
             * @var AbstractManager $manager
             */
            $manager = new $this->managerMap[$name]($this);
            $this->managers[$name] = $manager;
        }

        return $this->managers[$name];
    }

    /**
     * Magic method for fetching managers through magic properties.
     * @param string $name
     * @return AbstractManager|void
     * @throws ManagerNotFoundException
     * @internal
     */
    public function __get(string $name)
    {
        // If we have a manager with this name, return it.
        if ($this->hasManager($name)) {
            return $this->getManager($name);
        } else {
            throw new ManagerNotFoundException();
        }
    }
}
