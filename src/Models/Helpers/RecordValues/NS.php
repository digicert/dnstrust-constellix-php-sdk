<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers\RecordValues;

use Constellix\Client\Models\Helpers\RecordValue;

/**
 * Represents the data for NS records.
 * @package Constellix\Client\Models\RecordValues
 */
class NS extends RecordValue
{
    public bool $enabled = true;
    public string $host;
}
