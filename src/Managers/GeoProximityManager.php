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

    public function create(): GeoProximity
    {
        return $this->createObject();
    }

    public function get(int $id): GeoProximity
    {
        return $this->getObject($id);
    }
}
