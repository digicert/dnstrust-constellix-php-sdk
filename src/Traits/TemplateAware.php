<?php

declare(strict_types=1);

namespace Constellix\Client\Traits;

use Constellix\Client\Interfaces\Traits\TemplateAwareInterface;
use Constellix\Client\Models\Common\CommonTemplate;

trait TemplateAware
{
    protected CommonTemplate $template;

    /**
     * @param CommonTemplate $template
     * @return TemplateAwareInterface
     */
    public function setTemplate(CommonTemplate $template): TemplateAwareInterface
    {
        $this->template = $template;
        return $this;
    }
}
