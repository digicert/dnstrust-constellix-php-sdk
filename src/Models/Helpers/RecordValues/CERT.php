<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers\RecordValues;

use Constellix\Client\Models\Helpers\RecordValue;

class CERT extends RecordValue
{
    public bool $enabled = true;
    public string $certificateType;
    public string $keyTag;
    public string $algorithm;
    public string $certificate;
}
