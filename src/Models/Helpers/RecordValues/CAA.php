<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers\RecordValues;

use Constellix\Client\Models\Helpers\RecordValue;

class CAA extends RecordValue
{
    public bool $enabled = true;
    public string $tag;
    public string $flags;
    public string $data;
}
