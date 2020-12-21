<?php

declare(strict_types=1);

namespace Constellix\Client\Managers;

use Constellix\Client\Interfaces\Managers\GeoProximityManagerInterface;
use Constellix\Client\Interfaces\Models\GeoProximityInterface;

/**
 * Manages GeoProximity Location API resources.
 * @package Constellix\Client\Managers
 */
class GeoProximityManager extends AbstractManager implements GeoProximityManagerInterface
{
    /**
     * The base URI for the object.
     * @var string
     */
    protected string $baseUri = '/geoproximities';

    public function create(): GeoProximityInterface
    {
        return $this->createObject();
    }

    public function get(int $id): GeoProximityInterface
    {
        return $this->getObject($id);
    }
}