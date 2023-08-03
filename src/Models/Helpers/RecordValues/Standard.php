<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers\RecordValues;

use Constellix\Client\Models\Helpers\RecordValue;

class Standard extends RecordValue
{
    public string $value;
    public bool $enabled = true;
}
