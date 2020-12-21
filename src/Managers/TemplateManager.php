<?php

declare(strict_types=1);

namespace Constellix\Client\Managers;

use Constellix\Client\Interfaces\Managers\TemplateManagerInterface;
use Constellix\Client\Interfaces\Models\TemplateInterface;

/**
 * Manages Template API resources.
 * @package Constellix\Client\Managers
 */
class TemplateManager extends AbstractManager implements TemplateManagerInterface
{
    /**
     * The base URI for objects.
     * @var string
     */
    protected string $baseUri = '/templates';

    public function create(): TemplateInterface
    {
        return $this->createObject();
    }

    public function get(int $id): TemplateInterface
    {
        return $this->getObject($id);
    }
}