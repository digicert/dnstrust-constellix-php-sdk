<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces;

use Constellix\Client\Exceptions\Client\Http\HttpException;
use Constellix\Client\Exceptions\Client\JsonDecodeException;
use Constellix\Client\Interfaces\Managers\ContactListManagerInterface;
use Constellix\Client\Interfaces\Managers\DomainManagerInterface;
use Constellix\Client\Interfaces\Managers\FolderManagerInterface;
use Constellix\Client\Interfaces\Managers\GeoProximityManagerInterface;
use Constellix\Client\Interfaces\Managers\IPFilterManagerInterface;
use Constellix\Client\Interfaces\Managers\ManagedDomainManagerInterface;
use Constellix\Client\Interfaces\Managers\PoolManagerInterface;
use Constellix\Client\Interfaces\Managers\RecordFailoverManagerInterface;
use Constellix\Client\Interfaces\Managers\SecondaryDomainManagerInterface;
use Constellix\Client\Interfaces\Managers\SecondaryIPSetManagerInterface;
use Constellix\Client\Interfaces\Managers\SOARecordManagerInterface;
use Constellix\Client\Interfaces\Managers\TagManagerInterface;
use Constellix\Client\Interfaces\Managers\TemplateManagerInterface;
use Constellix\Client\Interfaces\Managers\TransferAclManagerInterface;
use Constellix\Client\Interfaces\Managers\UsageManagerInterface;
use Constellix\Client\Interfaces\Managers\VanityNameServerManagerInterface;
use DateTime;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Constellix API Client SDK
 *
 * @package Constellix\Client\Interfaces
 * @property-read TagManagerInterface $tags
 * @property-read ContactListManagerInterface $contactlists;
 * @property-read VanityNameserverManagerInterface $vanitynameservers;
 * @property-read GeoProximityManagerInterface $geoproximity;
 * @property-read IPFilterManagerInterface $ipfilters;
 * @property-read PoolManagerInterface $pools;
 * @property-read TemplateManagerInterface $templates;
 * @property-read DomainManagerInterface $domains;
 */
interface ClientInterface
{
    /**
     * Set a custom HTTP Client for all requests. If one is not provided, one is created automatically.
     * @param HttpClientInterface $client
     * @return ClientInterface
     */
    public function setHttpClient(HttpClientInterface $client): ClientInterface;

    /**
     * Fetches the current HTTP Client used for requests.
     * @return HttpClientInterface
     */
    public function getHttpClient(): HttpClientInterface;

    /**
     * Set the API endpoint to use. By default this is `https://api.dnsmadeeasy.com/V2.0`. You can set this to
     * `https://api.sandbox.dnsmadeeasy.com/V2.0` to use the Sandbox API.
     * @param string $endpoint
     * @return ClientInterface
     */
    public function setEndpoint(string $endpoint): ClientInterface;

    /**
     * Fetch the current API endpoint
     * @return string
     */
    public function getEndpoint(): string;

    /**
     * Sets the API key used for requests.
     * @param string $key
     * @return ClientInterface
     */
    public function setApiKey(string $key): ClientInterface;

    /**
     * Fetch the current API key.
     * @return string
     */
    public function getApiKey(): string;

    /**
     * Sets the secret key for requests.
     * @param string $key
     * @return ClientInterface
     */
    public function setSecretKey(string $key): ClientInterface;

    /**
     * Fetch the current secret key.
     * @return string
     */
    public function getSecretKey(): string;

    /**
     * This sets a Paginator Factory for the client. Any paginated responses will be created using the factory
     * specified. This is useful if you have a custom pagination class you want to use or one provided by a framework
     * such as the LengthAwarePaginator in Laravel.
     *
     * The default paginator used supports all the usual methods and properties you'd expect from a pagination class
     * and is iterable.
     *
     * @param PaginatorFactoryInterface $factory
     * @return ClientInterface
     */
    public function setPaginatorFactory(PaginatorFactoryInterface $factory): ClientInterface;

    /**
     * Fetch the current paginator factory interface.
     * @return PaginatorFactoryInterface
     */
    public function getPaginatorFactory(): PaginatorFactoryInterface;

    /**
     * Make a GET request to the API. The parameters will be encoded as query string parameters.
     * @param string $url
     * @param array $params
     * @return object|null
     * @throws HttpException
     * @throws JsonDecodeException
     */
    public function get(string $url, array $params = []): ?object;

    /**
     * Make a POST request to the API. The payload will be JSON encoded and sent in the body of the request with
     * `Content-Type: application/json` headers.
     * @param string $url
     * @param mixed|null $payload
     * @return object|null
     * @throws HttpException
     * @throws JsonDecodeException
     */
    public function post(string $url, $payload = null): ?object;

    /**
     * Make a PUT request to the API. The payload will be JSON encoded and sent in the body of the request with
     * `Content-Type: application/json` headers.
     * @param string $url
     * @param mixed|null $payload
     * @return object|null
     * @throws HttpException
     * @throws JsonDecodeException
     */
    public function put(string $url, $payload = null): ?object;

    /**
     * Make a DELETE request to the API.
     * @param string $url
     * @param mixed|null $payload
     * @return object|null
     * @throws HttpException
     * @throws JsonDecodeException
     */
    public function delete(string $url, $payload = null): ?object;

    /**
     * Makes a HTTP request to the API.
     * @param RequestInterface $request
     * @return object|null
     * @throws HttpException
     * @throws JsonDecodeException
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