<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers\RecordValues;

use Constellix\Client\Models\Helpers\RecordValue;

/**
 * Represents the standard mode data for A, AAAA, CNAME and ANAME records.
 * @package Constellix\Client\Models\RecordValues
 */
class Standard extends RecordValue
{
    public string $value;
    public bool $enabled = true;
}
