<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers\RecordValues;

use Constellix\Client\Models\Helpers\RecordValue;

/**
 * Represents the data for CERT records.
 * @package Constellix\Client\Models\RecordValues
 */
class CERT extends RecordValue
{
    public bool $enabled = true;
    public int $certificateType;
    public int $keyTag;
    public int $algorithm;
    public string $certificate;
}
