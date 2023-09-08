<?php

declare(strict_types=1);

namespace Constellix\Client\Models;

use Constellix\Client\Interfaces\Traits\TemplateAwareInterface;
use Constellix\Client\Managers\TemplateRecordManager;
use Constellix\Client\Traits\TemplateAware;

/**
 * Represents a Template Record resource.
 * @package Constellix\Client\Models
 *
 * @property string $name
 */
class TemplateRecord extends Record implements TemplateAwareInterface
{
    use TemplateAware;

    protected TemplateRecordManager $manager;
}
