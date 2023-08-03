<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers\RecordValues;

use Constellix\Client\Models\Common\CommonPool;
use Constellix\Client\Models\Helpers\RecordValue;

class Pool extends RecordValue
{
    public CommonPool $pool;

    public function transformForApi(): int
    {
        return $this->pool->id;
    }
}
