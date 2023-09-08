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

    /**
     * Create a new Tag.
     * @return Tag
     */
    public function create(): Tag
    {
        return $this->createObject();
    }

    /**
     * Fetch an existing Tag.
     * @param int $id
     * @return Tag
     * @throws \Constellix\Client\Exceptions\Client\Http\HttpException
     * @throws \Constellix\Client\Exceptions\Client\ModelNotFoundException
     * @throws \ReflectionException
     */

    public function get(int $id): Tag
    {
        return $this->getObject($id);
    }
}
