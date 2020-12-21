<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Managers;

use Constellix\Client\Enums\Pools\PoolType;
use Constellix\Client\Exceptions\Client\Http\HttpException;
use Constellix\Client\Exceptions\Client\ModelNotFoundException;
use Constellix\Client\Interfaces\Models\PoolInterface;

/**
 * Manages Contact List objects from the API.
 * @package Constellix\Client\Interfaces
 */
interface PoolManagerInterface extends AbstractManagerInterface
{
    /**
     * Creates a new Pool.
     * @return PoolInterface
     */
    public function create(): PoolInterface;

    /**
     * Gets the Pool with the specified type and ID.
     * @param PoolType $type
     * @param int $id
     * @return PoolInterface
     * @throws ModelNotFoundException
     * @throws HttpException
     */
    public function get(PoolType $type, int $id): PoolInterface;
}