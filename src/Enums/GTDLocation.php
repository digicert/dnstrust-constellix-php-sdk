<?php

declare(strict_types=1);

namespace Constellix\Client\Enums;

use Spatie\Enum\Enum;

/**
 * Enums to represent Global Traffic Director locations
 * @package Constellix\Client\Enums
 *
 * @method static self DEFAULT()
 * @method static self US_EAST()
 * @method static self US_WEST()
 * @method static self EUROPE()
 * @method static self ASIA_PAC()
 * @method static self OCEANIA()
 * @method static self SOUTH_AMERICA()
 */
class GTDLocation extends Enum
{
    /**
     * @return string[]
     */
    protected static function values(): array
    {
        return [
            'DEFAULT' => 'default',
            'EUROPE' => 'europe',
            'US_EAST' => 'us-east',
            'US_WEST' => 'us-west',
            'OCEANIA' => 'oceania',
            'ASIA_PAC' => 'asia-pacific',
            'SOUTH_AMERICA' => 'south-america',
        ];
    }
}
