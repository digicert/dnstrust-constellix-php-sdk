<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers\RecordValues;

use Constellix\Client\Models\Helpers\RecordValue;

/**
 * Represents the data for HINFO records.
 * @package Constellix\Client\Models\RecordValues
 */
class HINFO extends RecordValue
{
    public bool $enabled = true;
    public string $cpu;
    public string $os;
}
