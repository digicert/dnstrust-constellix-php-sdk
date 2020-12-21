<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers\RecordValues;

use Constellix\Client\Models\Helpers\RecordValue;

class NAPTR extends RecordValue
{
    public bool $enabled = true;
    public int $order;
    public int $preference;
    public $flags;
    public $service;
    public $regularExpression;
    public $replacement;
}