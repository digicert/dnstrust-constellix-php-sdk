<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Managers;

use Constellix\Client\Exceptions\Client\Http\HttpException;
use Constellix\Client\Exceptions\Client\ModelNotFoundException;
use Constellix\Client\Interfaces\Models\TagInterface;

/**
 * Manages Tag objects from the API.
 * @package Constellix\Client\Interfaces
 */
interface TagManagerInterface extends AbstractManagerInterface
{
    /**
     * Creates a new Tag.
     * @return TagInterface
     */
    public function create(): TagInterface;

    /**
     * Gets the Tag with the specified ID.
     * @param int $id
     * @return TagInterface
     * @throws ModelNotFoundException
     * @throws HttpException
     */
    public function get(int $id): TagInterface;
}