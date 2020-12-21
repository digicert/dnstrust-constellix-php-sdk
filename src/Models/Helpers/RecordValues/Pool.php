<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers\RecordValues;

use Constellix\Client\Interfaces\Models\Common\CommonPoolInterface;
use Constellix\Client\Models\Helpers\RecordValue;

class Pool extends RecordValue
{
    public CommonPoolInterface $pool;

    public function transformForApi()
    {
        return $this->pool->id;
    }
}