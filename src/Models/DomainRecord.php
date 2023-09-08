<?php

declare(strict_types=1);

namespace Constellix\Client\Models;

use Constellix\Client\Interfaces\Traits\DomainAwareInterface;
use Constellix\Client\Managers\DomainRecordManager;
use Constellix\Client\Traits\DomainAware;

/**
 * Represents a Domain Record resource.
 * @package Constellix\Client\Models
 *
 * @property string $name
 */
class DomainRecord extends Record implements DomainAwareInterface
{
    use DomainAware;

    protected DomainRecordManager $manager;
}
