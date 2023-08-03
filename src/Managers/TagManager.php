<?php

declare(strict_types=1);

namespace Constellix\Client\Managers;

use Constellix\Client\Models\Tag;

/**
 * Manages Tag API resources.
 * @package Constellix\Client\Managers
 */
class TagManager extends AbstractManager
{
    /**
     * The base URI for tags.
     * @var string
     */
    protected string $baseUri = '/tags';

    public function create(): Tag
    {
        return $this->createObject();
    }

    public function get(int $id): Tag
    {
        return $this->getObject($id);
    }
}
