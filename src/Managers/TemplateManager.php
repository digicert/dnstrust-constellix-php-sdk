<?php

declare(strict_types=1);

namespace Constellix\Client\Managers;

use Constellix\Client\Models\Template;

/**
 * Manages Template API resources.
 * @package Constellix\Client\Managers
 */
class TemplateManager extends AbstractManager
{
    /**
     * The base URI for objects.
     * @var string
     */
    protected string $baseUri = '/templates';

    public function create(): Template
    {
        return $this->createObject();
    }

    public function get(int $id): Template
    {
        return $this->getObject($id);
    }
}
