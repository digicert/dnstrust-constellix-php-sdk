<?php

declare(strict_types=1);

namespace Constellix\Client\Managers;

use Constellix\Client\Models\Template;
use Constellix\Client\Traits\HasPagination;

/**
 * Manages Template API resources.
 * @package Constellix\Client\Managers
 */
class TemplateManager extends AbstractManager
{
    use HasPagination;

    /**
     * The base URI for objects.
     * @var string
     */
    protected string $baseUri = '/templates';

    /**
     * Create a new Template.
     * @return Template
     */
    public function create(): Template
    {
        return $this->createObject();
    }

    /**
     * Fetch an existing Template.
     * @param int $id
     * @return Template
     * @throws \Constellix\Client\Exceptions\Client\Http\HttpException
     * @throws \Constellix\Client\Exceptions\Client\ModelNotFoundException
     * @throws \ReflectionException
     */
    public function get(int $id): Template
    {
        return $this->getObject($id);
    }
}
