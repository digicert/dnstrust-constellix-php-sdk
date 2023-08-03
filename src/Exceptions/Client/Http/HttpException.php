<?php

declare(strict_types=1);

namespace Constellix\Client\Exceptions\Client\Http;

use Constellix\Client\Exceptions\ConstellixException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Represents an exception while communicating with the Constellix API.
 * @package Constellix\Client\Exceptions\HTTP
 */
class HttpException extends ConstellixException
{
    /**
     * The request that was made when the exception was thrown.
     * @var RequestInterface|null
     */
    protected ?RequestInterface $request = null;

    /**
     * The response to the request.
     * @var ResponseInterface|null
     */
    protected ?ResponseInterface $response = null;

    /**
     * Set the request that caused the exception.
     * @param RequestInterface $request
     */
    public function setRequest(RequestInterface $request): void
    {
        $this->request = $request;
    }

    /**
     * Get the request that caused the exception.
     * @return RequestInterface|null
     */
    public function getRequest(): ?RequestInterface
    {
        return $this->request;
    }

    /**
     * Set the response that caused the exception.
     * @param ResponseInterface $response
     */
    public function setResponse(ResponseInterface $response): void
    {
        $this->response = $response;
    }

    /**
     * Get the response that caused the exception.
     * @return ResponseInterface|null
     */
    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }
}
