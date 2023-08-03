<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Basic;

use Constellix\Client\Models\Common\CommonPool;
use Constellix\Client\Models\Pool;

/**
 * Represents a basic representation of a Domain resource.
 * @package Constellix\Client\Models
 *

 * @property-read string $name
 * @property-read Pool $full
 */
class BasicPool extends CommonPool
{
    protected function getFull(): Pool
    {
        return $this->manager->get($this->type, $this->id);
    }
}
