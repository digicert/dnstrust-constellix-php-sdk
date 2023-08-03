<?php

declare(strict_types=1);

namespace Constellix\Client\Enums\Records;

use Spatie\Enum\Enum;

/**
 * Enums to represent record types
 * @package Constellix\Client\Enums
 *
 * @method static self STANDARD()
 * @method static self FAILOVER()
 * @method static self POOLS()
 * @method static self ROUNDROBINFAILOVER()
 */
class RecordMode extends Enum
{
    /**
     * @return string[]
     */
    protected static function values(): array
    {
        return [
            'STANDARD' => 'standard',
            'FAILOVER' => 'failover',
            'POOLS' => 'pools',
            'ROUNDROBINFAILOVER' => 'roundRobinFailover',
        ];
    }
}
