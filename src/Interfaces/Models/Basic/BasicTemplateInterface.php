<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Models\Basic;

use Constellix\Client\Interfaces\Models\Common\CommonTemplateInterface;
use Constellix\Client\Interfaces\Models\TemplateInterface;

/**
 * Represents a Template resource
 * @package Constellix\Client\Interfaces
 *
 * @property-read string $name
 * @property-read TemplateInterface $full
 */
interface BasicTemplateInterface extends CommonTemplateInterface
{
}