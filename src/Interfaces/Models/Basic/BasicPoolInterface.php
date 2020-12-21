<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Models\Basic;

use Constellix\Client\Interfaces\Models\Common\CommonPoolInterface;
use Constellix\Client\Interfaces\Models\PoolInterface;

/**
 * Represents a Pool resource
 * @package Constellix\Client\Interfaces
 *
 * @property-read PoolInterface $full
 */
interface BasicPoolInterface extends CommonPoolInterface
{
}