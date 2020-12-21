<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Models\Helpers;

use Constellix\Client\Enums\Pools\ITOHandicapFactor;
use Constellix\Client\Enums\Pools\ITORegion;
use Constellix\Client\Interfaces\Models\AbstractModelInterface;

/**
 * Represents specific ITO configuration
 * @package Constellix\Client\Interfaces
 *
 * @property int $frequency
 * @property int $maximumNumberOfResults
 * @property int $deviationAllowance
 * @property ITORegion $monitoringRegion
 * @property ITOHandicapFactor $handicapFactor
 */
interface ITOConfigInterface extends AbstractModelInterface
{
}