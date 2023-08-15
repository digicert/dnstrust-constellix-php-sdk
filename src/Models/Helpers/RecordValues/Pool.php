<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers\RecordValues;

use Constellix\Client\Models\Helpers\RecordValue;

class Pool extends RecordValue
{
    public \Constellix\Client\Models\Pool $pool;

    public function transformForApi(): int
    {
        return $this->pool->id;
    }
}
