<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Traits;

use Constellix\Client\Interfaces\Models\Common\CommonDomainInterface;

/**
 * Trait for objects that know about domains
 *
 * @package Constellix\Client\Interfaces
 *
 * @property-read CommonDomainInterface $domain
 */
interface DomainAwareInterface
{
    public function setDomain(CommonDomainInterface $domain): DomainAwareInterface;
}