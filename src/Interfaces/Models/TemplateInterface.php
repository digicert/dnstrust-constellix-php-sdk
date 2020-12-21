<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Models;

use Constellix\Client\Interfaces\Models\Common\CommonTemplateInterface;

/**
 * Represents a Template resource
 * @package Constellix\Client\Interfaces
 *
 * @property string $name
 * @property-read int $version
 * @property bool $geoip
 * @property bool $gtd
 * @property \DateTime $createdAt
 * @property \DateTime $updatedAt
 */
interface TemplateInterface extends CommonTemplateInterface
{
}