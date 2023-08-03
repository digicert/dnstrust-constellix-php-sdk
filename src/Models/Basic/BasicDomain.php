<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Basic;

use Constellix\Client\Models\Common\CommonDomain;
use Constellix\Client\Models\Domain;

/**
 * Represents a basic representation of a Domain resource.
 * @package Constellix\Client\Models
 *

 * @property-read string $name
 * @property-read Domain $full
 */
class BasicDomain extends CommonDomain
{
    protected function getFull(): Domain
    {
        return $this->manager->get($this->id);
    }
}
