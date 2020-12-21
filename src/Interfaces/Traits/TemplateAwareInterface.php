<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Traits;

use Constellix\Client\Interfaces\Models\Common\CommonTemplateInterface;

/**
 * Trait for objects that know about templates.
 *
 * @package Constellix\Client\Interfaces
 *
 * @property-read CommonTemplateInterface $template
 */
interface TemplateAwareInterface
{
    public function setTemplate(CommonTemplateInterface $template): TemplateAwareInterface;
}