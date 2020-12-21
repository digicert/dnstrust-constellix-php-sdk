<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Models\Basic;

use Constellix\Client\Interfaces\Models\Common\CommonDomainInterface;
use Constellix\Client\Interfaces\Models\DomainInterface;

/**
 * Represents a Domain resource
 * @package Constellix\Client\Interfaces
 *
 * @property-read string $name
 * @property-read DomainInterface $full
 */
interface BasicDomainInterface extends CommonDomainInterface
{
}