<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Traits;

use Constellix\Client\Models\Domain;

/**
 * Trait for objects that know about domains
 *
 * @package Constellix\Client\Interfaces
 *
 * @property-read Domain $domain
 */
interface DomainAwareInterface
{
    public function setDomain(Domain $domain): DomainAwareInterface;
}
