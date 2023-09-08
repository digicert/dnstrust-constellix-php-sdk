<?php

declare(strict_types=1);

namespace Constellix\Client\Traits;

use Constellix\Client\Interfaces\Traits\TemplateAwareInterface;
use Constellix\Client\Models\Template;

trait TemplateAware
{
    /**
     * @var Template The Template for this object
     */
    public Template $template;

    /**
     * Set the Template for this object.
     * @param Template $template
     * @return TemplateAwareInterface
     */
    public function setTemplate(Template $template): TemplateAwareInterface
    {
        $this->template = $template;
        return $this;
    }
}
