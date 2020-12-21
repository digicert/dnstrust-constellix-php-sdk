<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Basic;

use Constellix\Client\Interfaces\Models\Basic\BasicTemplateInterface;
use Constellix\Client\Interfaces\Models\TemplateInterface;
use Constellix\Client\Models\Common\CommonTemplate;

/**
 * Represents a concise representation of a Template resource.
 * @package Constellix\Client\Models
 *
 * @property-read string $name
 * @property-read TemplateInterface $full
 */
class BasicTemplate extends CommonTemplate implements BasicTemplateInterface
{
    protected function getFull()
    {
        return $this->manager->get($this->id);
    }
}