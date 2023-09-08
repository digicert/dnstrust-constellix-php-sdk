<?php

declare(strict_types=1);

namespace Constellix\Client\Enums\Pools;

use Spatie\Enum\Enum;

/**
 * Enums to represent ITO Regions
 * @package Constellix\Client\Enums
 *
 * @method static self NONE()
 * @method static self PERCENT()
 * @method static self SPEED()
 */
class ITOHandicapFactor extends Enum
{
    /**
     * @return string[]
     */
    protected static function values(): array
    {
        return [
            'NONE' => 'none',
            'PERCENT' => 'percent',
            'SPEED' => 'speed',
        ];
    }
}
