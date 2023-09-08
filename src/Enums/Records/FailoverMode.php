<?php

declare(strict_types=1);

namespace Constellix\Client\Enums\Records;

use Spatie\Enum\Enum;

/**
 * Enums to represent record types
 * @package Constellix\Client\Enums
 *
 * @method static self NORMAL()
 * @method static self OFF()
 * @method static self ONE_WAY()
 */
class FailoverMode extends Enum
{
    /**
     * @return string[]
     */
    protected static function values(): array
    {
        return [
            'NORMAL' => 'normal',
            'OFF' => 'off',
            'ONE_WAY' => 'one-way',
        ];
    }
}
