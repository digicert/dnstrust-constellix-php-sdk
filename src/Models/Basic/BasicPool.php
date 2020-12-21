<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Basic;

use Constellix\Client\Interfaces\Models\Basic\BasicPoolInterface;
use Constellix\Client\Interfaces\Models\PoolInterface;
use Constellix\Client\Models\Common\CommonPool;

/**
 * Represents a basic representation of a Domain resource.
 * @package Constellix\Client\Models
 *

 * @property-read string $name
 * @property-read PoolInterface $full
 */
class BasicPool extends CommonPool implements BasicPoolInterface
{
    protected function getFull()
    {
        return $this->manager->get($this->id);
    }
}