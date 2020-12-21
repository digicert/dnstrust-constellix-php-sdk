<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Models;

use Constellix\Client\Enums\Pools\PoolValuePolicy;

/**
 * Represents a Pool Value resource
 * @package Constellix\Client\Interfaces
 *
 * @property string $value
 * @property int $weight
 * @property-read bool $enabled
 * @property float $handicap
 * @property PoolValuePolicy $policy
 * @property int $sonarCheckId
 * @property-read bool $activated
 * @property-read bool $failed
 * @property-read float $speed
 */
interface PoolValueInterface extends AbstractModelInterface
{
}