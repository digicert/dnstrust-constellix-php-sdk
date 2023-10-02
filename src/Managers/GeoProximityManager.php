<?php

declare(strict_types=1);

namespace Constellix\Client\Managers;

use Constellix\Client\Models\GeoProximity;
use Constellix\Client\Traits\HasPagination;

/**
 * Manages GeoProximity Location API resources.
 * @package Constellix\Client\Managers
 */
class GeoProximityManager extends AbstractManager
{
    use HasPagination;

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
        /**
         * @var GeoProximity $object
         */
        $object = $this->createObject();
        return $object;
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
        /**
         * @var GeoProximity $object
         */
        $object = $this->getObject($id);
        return $object;
    }
}
