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
    /**
     * Set the domain that relates to this object.
     * @param Domain $domain
     * @return DomainAwareInterface
     */
    public function setDomain(Domain $domain): DomainAwareInterface;
}
