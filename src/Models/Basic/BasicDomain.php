<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Basic;

use Constellix\Client\Interfaces\Models\Basic\BasicDomainInterface;
use Constellix\Client\Interfaces\Models\DomainInterface;
use Constellix\Client\Models\Common\CommonDomain;

/**
 * Represents a basic representation of a Domain resource.
 * @package Constellix\Client\Models
 *

 * @property-read string $name
 * @property-read DomainInterface $full
 */
class BasicDomain extends CommonDomain implements BasicDomainInterface
{
    protected function getFull()
    {
        return $this->manager->get($this->id);
    }
}