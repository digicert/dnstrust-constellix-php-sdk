<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers\RecordValues;

use Constellix\Client\Models\Helpers\RecordValue;

class SPF extends RecordValue
{
    public bool $enabled = true;
    public $value;
}