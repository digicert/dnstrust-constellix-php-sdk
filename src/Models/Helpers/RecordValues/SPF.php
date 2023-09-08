<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers\RecordValues;

use Constellix\Client\Models\Helpers\RecordValue;

/**
 * Represents the data for SPF records.
 * @package Constellix\Client\Models\RecordValues
 */
class SPF extends RecordValue
{
    public bool $enabled = true;
    public string $value;
}
