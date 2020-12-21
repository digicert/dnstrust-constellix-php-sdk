<?php

declare(strict_types=1);

namespace Constellix\Client\Traits;

use Constellix\Client\Interfaces\Models\Common\CommonTemplateInterface;
use Constellix\Client\Interfaces\Traits\TemplateAwareInterface;

trait TemplateAware
{
    protected ?CommonTemplateInterface $template = null;

    public function setTemplate(CommonTemplateInterface $template): TemplateAwareInterface
    {
        $this->template = $template;
        return $this;
    }
}