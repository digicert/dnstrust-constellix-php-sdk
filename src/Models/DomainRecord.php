<?php

declare(strict_types=1);

namespace Constellix\Client\Models;

use Constellix\Client\Interfaces\Models\DomainRecordInterface;
use Constellix\Client\Interfaces\Traits\DomainAwareInterface;
use Constellix\Client\Traits\DomainAware;

/**
 * Represents a Domain Record resource.
 * @package Constellix\Client\Models
 *
 * @property string $name
 */
class DomainRecord extends Record implements DomainRecordInterface, DomainAwareInterface
{
    use DomainAware;
}