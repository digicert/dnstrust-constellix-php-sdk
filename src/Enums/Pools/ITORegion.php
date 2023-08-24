<?php

declare(strict_types=1);

namespace Constellix\Client\Enums\Pools;

use Spatie\Enum\Enum;

/**
 * Enums to represent ITO Regions
 * @package Constellix\Client\Enums
 *
 * @method static self WORLD()
 * @method static self ASIAPAC()
 * @method static self EUROPE()
 * @method static self NACENTRAL()
 * @method static self NAEAST()
 * @method static self NAWEST()
 * @method static self OCEANIA()
 * @method static self SOUTHAMERICA()
 */
class ITORegion extends Enum
{
    /**
     * @return string[]
     */
    protected static function values(): array
    {
        return [
            'WORLD' => 'world',
            'ASIAPAC' => 'asiapac',
            'EUROPE' => 'europe',
            'NACENTRAL' => 'nacentral',
            'NAEAST' => 'naeast',
            'NAWEST' => 'nawest',
            'OCEANIA' => 'oceania',
            'SOUTHAMERICA' => 'southamerica',
        ];
    }
}
