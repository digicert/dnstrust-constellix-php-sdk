<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Models\Common;

use Constellix\Client\Enums\Pools\PoolType;
use Constellix\Client\Interfaces\Models\AbstractModelInterface;
use Constellix\Client\Interfaces\Models\Concise\ConciseContactListInterface;
use Constellix\Client\Interfaces\Models\Concise\BasicDomainInterface;
use Constellix\Client\Interfaces\Models\Concise\BasicTemplateInterface;
use Constellix\Client\Interfaces\Models\ITOInterface;

/**
 * Represents a Pool resource
 * @package Constellix\Client\Interfaces
 *
 * @property PoolType $type
 * @property string $name
 */
interface CommonPoolInterface extends AbstractModelInterface
{
}