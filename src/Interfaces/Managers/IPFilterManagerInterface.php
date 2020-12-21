<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Managers;

use Constellix\Client\Exceptions\Client\Http\HttpException;
use Constellix\Client\Exceptions\Client\ModelNotFoundException;
use Constellix\Client\Interfaces\Models\IPFilterInterface;

/**
 * Manages IP Filter objects from the API.
 * @package Constellix\Client\Interfaces
 */
interface IPFilterManagerInterface extends AbstractManagerInterface
{
    /**
     * Creates a new IP Filter.
     * @return IPFilterInterface
     */
    public function create(): IPFilterInterface;

    /**
     * Gets the IP Filter with the specified ID.
     * @param int $id
     * @return IPFilterInterface
     * @throws ModelNotFoundException
     * @throws HttpException
     */
    public function get(int $id): IPFilterInterface;
}