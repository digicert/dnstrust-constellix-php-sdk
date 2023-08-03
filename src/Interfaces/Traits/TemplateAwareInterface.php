<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Traits;

use Constellix\Client\Models\Common\CommonTemplate;

/**
 * Trait for objects that know about templates.
 *
 * @package Constellix\Client\Interfaces
 *
 * @property-read CommonTemplate $template
 */
interface TemplateAwareInterface
{
    public function setTemplate(CommonTemplate $template): TemplateAwareInterface;
}
