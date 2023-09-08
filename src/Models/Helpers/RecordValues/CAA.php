<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers\RecordValues;

use Constellix\Client\Models\Helpers\RecordValue;

/**
 * Represents the data for CAA records.
 * @package Constellix\Client\Models\RecordValues
 */
class CAA extends RecordValue
{
    public bool $enabled = true;
    public string $tag;
    public int $flags;
    public string $data;
}
