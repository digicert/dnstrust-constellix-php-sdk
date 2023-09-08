<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers\RecordValues;

use Constellix\Client\Models\Helpers\RecordValue;

/**
 * Represents the data for SRV records.
 * @package Constellix\Client\Models\RecordValues
 */
class SRV extends RecordValue
{
    public bool $enabled = true;
    public int $priority;
    public int $port;
    public int $weight;
    public string $host;
}
