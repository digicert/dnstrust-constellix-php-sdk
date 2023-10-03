<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces;

use Constellix\Client\Exceptions\Client\Http\HttpException;
use Constellix\Client\Exceptions\Client\JsonDecodeException;
use Constellix\Client\Managers\ContactListManager;
use Constellix\Client\Managers\DomainManager;
use Constellix\Client\Managers\GeoProximityManager;
use Constellix\Client\Managers\IPFilterManager;
use Constellix\Client\Managers\PoolManager;
use Constellix\Client\Managers\TagManager;
use Constellix\Client\Managers\TemplateManager;
use Constellix\Client\Managers\VanityNameserverManager;
use DateTime;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;

/**
 * An interface for the Constellix API Client.
 * @package Constellix\Client\Interfaces
 *
 * @property-read TagManager $tags Manager for Tags
 * @property-read ContactListManager $contactlists Manager for Contact Lists
 * @property-read VanityNameserverManager $vanitynameservers Manager for Vanity Nameservers
 * @property-read GeoProximityManager $geoproximity Manager for Geoproximities
 * @property-read IPFilterManager $ipfilters Manager for IP Filters
 * @property-read PoolManager $pools Manager for Pools
 * @property-read TemplateManager $templates Manager for Templates
 * @property-read DomainManager $domains Manager for Domains
 */
interface ConstellixApiClient
{
    /**
     * Sets the HTTP Client for requests to the API. It must be PSR-18 compatible.
     * @param HttpClientInterface $client
     * @return ConstellixApiClient
     */
    public function setHttpClient(HttpClientInterface $client): ConstellixApiClient;

    /**
     * Returns the current HTTP client.
     * @return HttpClientInterface
     */
    public function getHttpClient(): ?HttpClientInterface;

    /**
     * Sets the logger to use. The Logger must be PSR-3 compatible.
     * @param LoggerInterface $logger
     * @return void
     */
    public function setLogger(LoggerInterface $logger): void;
    /**
     * Returns the current logger.
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface;

    /**
     * Set the API endpoint for the Constellix DNS v4 API,
     * @param string $endpoint
     * @return ConstellixApiClient
     */
    public function setEndpoint(string $endpoint): ConstellixApiClient;

    /**
     * Return the current API endpoint,
     * @return string
     */
    public function getEndpoint(): string;

    /**
     * Set the Constellix API Key.
     * @param string $key
     * @return ConstellixApiClient
     */
    public function setApiKey(string $key): ConstellixApiClient;

    /**
     * Fetch the current Constellix API Key.
     * @return ?string
     */
    public function getApiKey(): ?string;

    /**
     * Set the Constellix Secret Key.
     * @param string $key
     * @return ConstellixApiClient
     */
    public function setSecretKey(string $key): ConstellixApiClient;

    /**
     * Fetch the current Constellix Secret Key.
     * @return ?string
     */
    public function getSecretKey(): ?string;

    /**
     * Set the pagination factory to use. This factory will be used to construct all paginated results from the managers.
     * @param PaginatorFactoryInterface $factory
     * @return ConstellixApiClient
     */
    public function setPaginatorFactory(PaginatorFactoryInterface $factory): ConstellixApiClient;

    /**
     * Return the current pagination factory.
     * @return PaginatorFactoryInterface
     */
    public function getPaginatorFactory(): PaginatorFactoryInterface;

    /**
     * Make a GET request to the Constellix API.
     * @param string $url
     * @param array<mixed> $params
     * @return object|null
     * @throws HttpException
     * @throws JsonDecodeException
     * @internal
     */
    public function get(string $url, array $params = []): ?object;

    /**
     * Make a POST request to the Constellix API.
     * @param string $url
     * @param mixed|null $payload
     * @return object|null
     * @throws HttpException
     * @throws JsonDecodeException
     * @internal
     */
    public function post(string $url, $payload = null): ?object;

    /**
     * Make a PUT request to the Constellix API.
     * @param string $url
     * @param mixed|null $payload
     * @return object|null
     * @throws HttpException
     * @throws JsonDecodeException
     * @internal
     */
    public function put(string $url, $payload = null): ?object;

    /**
     * Make a DELETE request to the Constellix API.
     * @param string $url
     * @param mixed|null $payload
     * @return object|null
     * @throws HttpException
     * @throws JsonDecodeException
     * @internal
     */
    public function delete(string $url, $payload = null): ?object;

    /**
     * Build and send a request to the API.
     * @param RequestInterface $request
     * @return object|null
     * @throws HttpException
     * @throws JsonDecodeException
     * @internal
     */
    public function send(RequestInterface $request): ?object;

    /**
     * Get the request limit.
     * @return int|null
     */
    public function getRequestLimit(): ?int;

    /**
     * Get the number of requests remaining before you hit the request limit.
     * @return int|null
     */
    public function getRequestsRemaining(): ?int;

    /**
     * Get the datetime that the request limit resets.
     * @return DateTime|null
     */
    public function getLimitReset(): ?DateTime;
}
