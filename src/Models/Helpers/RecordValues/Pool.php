<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers\RecordValues;

use Constellix\Client\Models\Helpers\RecordValue;

/**
 * Represents the pool mode data for A, AAAA, CNAME and ANAME records.
 * @package Constellix\Client\Models\RecordValues
 */
class Pool extends RecordValue
{
    public \Constellix\Client\Models\Pool $pool;

    public function transformForApi(): mixed
    {
        return $this->pool->id;
    }
}
