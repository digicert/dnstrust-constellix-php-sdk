<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Basic;

use Constellix\Client\Models\Common\CommonTemplate;
use Constellix\Client\Models\Template;

/**
 * Represents a concise representation of a Template resource.
 * @package Constellix\Client\Models
 *
 * @property-read string $name
 * @property-read Template $full
 */
class BasicTemplate extends CommonTemplate
{
    protected function getFull(): Template
    {
        return $this->manager->get($this->id);
    }
}
