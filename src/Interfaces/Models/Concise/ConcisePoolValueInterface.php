<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Models\Concise;

use Constellix\Client\Interfaces\Models\Common\CommonPoolValueInterface;

/**
 * Represents a Pool Value resource
 * @package Constellix\Client\Interfaces
 *
 * @property-read string $value
 * @property-read int $weight
 */
interface ConcisePoolValueInterface extends CommonPoolValueInterface
{
}