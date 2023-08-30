<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Traits;

use Constellix\Client\Interfaces\Managers\AbstractManagerInterface;

/**
 * Trait for models which can be edited.
 *
 * @package Constellix\Client\Interfaces
 *
 * @property-read int $id
 */
interface EditableModelInterface
{
    /**
     * Saves the object.
     */
    public function save(): void;

    /**
     * Deletes the object.
     */
    public function delete(): void;

    /**
     * Returns true if the object has been modified since it was fetched.
     *
     * @return bool
     */
    public function hasChanged(): bool;

    /**
     * Fetch the latest version of the object from the API. This will overwrite changes that have been made.
     */
    public function refresh(): void;
}
