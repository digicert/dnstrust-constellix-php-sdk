<?php

declare(strict_types=1);

namespace Constellix\Client\Traits;

use Constellix\Client\Interfaces\Traits\DomainAwareInterface;
use Constellix\Client\Models\Domain;

trait DomainAware
{
    protected Domain $domain;

    public function setDomain(Domain $domain): DomainAwareInterface
    {
        $this->domain = $domain;
        return $this;
    }
}
