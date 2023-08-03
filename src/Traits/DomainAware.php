<?php

declare(strict_types=1);

namespace Constellix\Client\Traits;

use Constellix\Client\Interfaces\Traits\DomainAwareInterface;
use Constellix\Client\Models\Common\CommonDomain;

trait DomainAware
{
    protected CommonDomain $domain;

    public function setDomain(CommonDomain $domain): DomainAwareInterface
    {
        $this->domain = $domain;
        return $this;
    }
}
