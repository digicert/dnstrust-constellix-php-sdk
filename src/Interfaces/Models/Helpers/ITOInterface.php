<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Models\Helpers;

use Constellix\Client\Interfaces\Models\AbstractModelInterface;

/**
 * Represents ITO configuration
 * @package Constellix\Client\Interfaces
 *
 * @property bool $enabled
 * @property ITOConfigInterface $config
 */
interface ITOInterface extends AbstractModelInterface
{
}