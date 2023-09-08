<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers\RecordValues;

use Constellix\Client\Models\Helpers\RecordValue;

/**
 * Represents the data for PTR records.
 * @package Constellix\Client\Models\RecordValues
 */
class PTR extends RecordValue
{
    public bool $enabled = true;
    public string $system;
}
