<?php

declare(strict_types=1);

namespace Constellix\Client\Managers;

use Constellix\Client\Models\GeoProximity;

/**
 * Manages GeoProximity Location API resources.
 * @package Constellix\Client\Managers
 */
class GeoProximityManager extends AbstractManager
{
    /**
     * The base URI for the object.
     * @var string
     */
    protected string $baseUri = '/geoproximities';

    /**
     * Create a new GeoProximity Location.
     * @return GeoProximity
     */
    public function create(): GeoProximity
    {
        return $this->createObject();
    }

    /**
     * Fetch an existing GeoProximity Location
     * @param int $id
     * @return GeoProximity
     * @throws \Constellix\Client\Exceptions\Client\Http\HttpException
     * @throws \Constellix\Client\Exceptions\Client\ModelNotFoundException
     * @throws \ReflectionException
     */
    public function get(int $id): GeoProximity
    {
        return $this->getObject($id);
    }
}
