<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers\RecordValues;

use Constellix\Client\Models\Helpers\RecordValue;

/**
 * Represents the data in a TXT record.
 * @package Constellix\Client\Models\RecordValues
 */
class TXT extends RecordValue
{
    public bool $enabled = true;
    public string $value;
}
