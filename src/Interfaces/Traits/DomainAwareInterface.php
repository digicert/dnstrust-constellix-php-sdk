<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Traits;

use Constellix\Client\Models\Common\CommonDomain;

/**
 * Trait for objects that know about domains
 *
 * @package Constellix\Client\Interfaces
 *
 * @property-read CommonDomain $domain
 */
interface DomainAwareInterface
{
    public function setDomain(CommonDomain $domain): DomainAwareInterface;
}
