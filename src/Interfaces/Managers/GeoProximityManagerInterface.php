<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Managers;

use Constellix\Client\Exceptions\Client\Http\HttpException;
use Constellix\Client\Exceptions\Client\ModelNotFoundException;
use Constellix\Client\Interfaces\Models\GeoProximityInterface;

/**
 * Manages GeoProximity Location objects from the API.
 * @package Constellix\Client\Interfaces
 */
interface GeoProximityManagerInterface extends AbstractManagerInterface
{
    /**
     * Creates a new GeoProximity Location.
     * @return GeoProximityInterface
     */
    public function create(): GeoProximityInterface;

    /**
     * Gets the GeoProximity Location with the specified ID.
     * @param int $id
     * @return GeoProximityInterface
     * @throws ModelNotFoundException
     * @throws HttpException
     */
    public function get(int $id): GeoProximityInterface;
}