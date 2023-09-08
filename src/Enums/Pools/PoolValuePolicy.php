<?php

declare(strict_types=1);

namespace Constellix\Client\Enums\Pools;

use Spatie\Enum\Enum;

/**
 * Enums to represent Pool Value policies
 * @package Constellix\Client\Enums
 *
 * @method static self FOLLOW_SONAR()
 * @method static self ALWAYS_OFF()
 * @method static self ALWAYS_ON()
 * @method static self OFF_ON_FAILURE()
 */
class PoolValuePolicy extends Enum
{
    /**
     * @return string[]
     */
    protected static function values(): array
    {
        return [
            'FOLLOW_SONAR' => 'follow_sonar',
            'ALWAYS_OFF' => 'always_off',
            'ALWAYS_ON' => 'always_on',
            'OFF_ON_FAILURE' => 'off_on_failure',
        ];
    }
}
