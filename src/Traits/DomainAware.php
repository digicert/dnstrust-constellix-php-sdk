<?php

declare(strict_types=1);

namespace Constellix\Client\Traits;

use Constellix\Client\Interfaces\Models\Common\CommonDomainInterface;
use Constellix\Client\Interfaces\Traits\DomainAwareInterface;

trait DomainAware
{
    protected ?CommonDomainInterface $domain = null;

    public function setDomain(CommonDomainInterface $domain): DomainAwareInterface
    {
        $this->domain = $domain;
        return $this;
    }
}