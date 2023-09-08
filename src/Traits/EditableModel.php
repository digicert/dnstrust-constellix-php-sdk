<?php

declare(strict_types=1);

namespace Constellix\Client\Traits;

trait EditableModel
{
    /**
     * Check if the object or specific property in the object has been changed locally since it was
     * fetched from the API.
     * @param string|null $property
     * @return bool
     */
    public function hasChanged(?string $property = null): bool
    {
        if ($property === null) {
            return (bool)$this->changed;
        }

        return in_array($property, $this->changed);
    }

    /**
     * Save the object.
     * @return void
     * @throws \Constellix\Client\Exceptions\Client\Http\HttpException
     */
    public function save(): void
    {
        if ($this->id && !$this->hasChanged()) {
            return;
        }
        $this->manager->save($this);
        $this->originalProps = $this->props;
        $this->changed = [];
    }

    /**
     * Delete the object.
     * @return void
     * @throws \Constellix\Client\Exceptions\Client\Http\HttpException
     */
    public function delete(): void
    {
        if (!$this->id) {
            return;
        }
        $this->manager->delete($this);
    }
}
