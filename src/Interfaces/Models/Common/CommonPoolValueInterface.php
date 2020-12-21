<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Models\Common;

use Constellix\Client\Interfaces\Models\AbstractModelInterface;

/**
 * Represents a Pool Value resource
 * @package Constellix\Client\Interfaces
 *
 * @property-read string $value
 * @property-read int $weight
 */
interface CommonPoolValueInterface extends AbstractModelInterface
{
}