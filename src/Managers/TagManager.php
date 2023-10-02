<?php

declare(strict_types=1);

namespace Constellix\Client\Managers;

use Constellix\Client\Models\Tag;
use Constellix\Client\Traits\HasPagination;

/**
 * Manages Tag API resources.
 * @package Constellix\Client\Managers
 */
class TagManager extends AbstractManager
{
    use HasPagination;

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
        /**
         * @var Tag $object
         */
        $object = $this->createObject();
        return $object;
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
        /**
         * @var Tag $object
         */
        $object = $this->getObject($id);
        return $object;
    }
}
