<?php

declare(strict_types=1);

namespace Constellix\Client\Models;

use Constellix\Client\Interfaces\Models\TemplateRecordInterface;
use Constellix\Client\Interfaces\Traits\TemplateAwareInterface;
use Constellix\Client\Traits\TemplateAware;

/**
 * Represents a Template Record resource.
 * @package Constellix\Client\Models
 *
 * @property string $name
 */
class TemplateRecord extends Record implements TemplateRecordInterface, TemplateAwareInterface
{
    use TemplateAware;
}