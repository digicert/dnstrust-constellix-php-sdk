<?php

declare(strict_types=1);

namespace Constellix\Client\Managers;

use Constellix\Client\Interfaces\Managers\TagManagerInterface;
use Constellix\Client\Interfaces\Models\TagInterface;

/**
 * Manages Tag API resources.
 * @package Constellix\Client\Managers
 */
class TagManager extends AbstractManager implements TagManagerInterface
{
    /**
     * The base URI for tags.
     * @var string
     */
    protected string $baseUri = '/tags';

    public function create(): TagInterface
    {
        return $this->createObject();
    }

    public function get(int $id): TagInterface
    {
        return $this->getObject($id);
    }
}