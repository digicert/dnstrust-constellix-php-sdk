<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers\RecordValues;

use Constellix\Client\Models\Helpers\RecordValue;

class Standard extends RecordValue
{
    public $value;
    public bool $enabled = true;
}