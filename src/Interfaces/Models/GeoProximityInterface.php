<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Models;

/**
 * Represents a Geo Proximity Location resource
 * @package Constellix\Client\Interfaces
 *
 * @property string $name
 * @property string $country
 * @property string $region
 * @property string $city
 * @property float $longitude
 * @property float $latitude
 */
interface GeoProximityInterface extends AbstractModelInterface
{
}