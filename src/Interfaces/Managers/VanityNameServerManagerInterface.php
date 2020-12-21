<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Managers;

use Constellix\Client\Exceptions\Client\Http\HttpException;
use Constellix\Client\Exceptions\Client\ModelNotFoundException;
use Constellix\Client\Interfaces\Models\VanityNameserverInterface;

/**
 * Manages Vanity NameServer objects from the API.
 * @package Constellix\Client\Interfaces
 */
interface VanityNameServerManagerInterface extends AbstractManagerInterface
{
    /**
     * Creates a new Vanity NameServer.
     * @return VanityNameserverInterface
     */
    public function create(): VanityNameserverInterface;

    /**
     * Gets the Vanity NameServer with the specified ID.
     * @param int $id
     * @return VanityNameserverInterface
     * @throws ModelNotFoundException
     * @throws HttpException
     */
    public function get(int $id): VanityNameserverInterface;
}