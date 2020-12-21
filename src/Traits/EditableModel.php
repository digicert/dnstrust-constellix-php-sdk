<?php

declare(strict_types=1);

namespace Constellix\Client\Traits;

trait EditableModel
{
    public function hasChanged(): bool
    {
        return (bool)$this->changed;
    }

    public function save(): void
    {
        if ($this->id && !$this->hasChanged()) {
            return;
        }
        $this->manager->save($this);
        $this->originalProps = $this->props;
        $this->changed = [];
    }

    public function delete(): void
    {
        if (!$this->id) {
            return;
        }
        $this->manager->delete($this);
    }

    public function refresh(): void
    {
        $this->manager->refresh($this);
    }
}