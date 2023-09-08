<?php

declare(strict_types=1);

namespace Constellix\Client\Traits;

use Constellix\Client\Interfaces\Traits\DomainAwareInterface;
use Constellix\Client\Models\Domain;

trait DomainAware
{
    /**
     * @var Domain The Domain for this object
     */
    public Domain $domain;

    /**
     * Set the Domain for this object.
     * @param Domain $domain
     * @return DomainAwareInterface
     */
    public function setDomain(Domain $domain): DomainAwareInterface
    {
        $this->domain = $domain;
        return $this;
    }
}
