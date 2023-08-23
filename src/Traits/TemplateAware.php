<?php

declare(strict_types=1);

namespace Constellix\Client\Traits;

use Constellix\Client\Interfaces\Traits\TemplateAwareInterface;
use Constellix\Client\Models\Template;

trait TemplateAware
{
    public Template $template;

    /**
     * @param Template $template
     * @return TemplateAwareInterface
     */
    public function setTemplate(Template $template): TemplateAwareInterface
    {
        $this->template = $template;
        return $this;
    }
}
