<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers;

abstract class RecordValue
{
    /**
     * @param array<mixed> $data
     */
    public function __construct(array $data = [])
    {
        if ($data) {
            $this->populateFromApi($data);
        }
    }

    /**
     * @param array<mixed> $data
     * @return array<mixed>
     */
    protected function parseApiData(array $data): array
    {
        return $data;
    }

    /**
     * @param array<mixed> $data
     * @return self
     */
    public function populateFromApi(array $data): self
    {
        $data = $this->parseApiData($data);
        foreach ($data as $prop => $name) {
            $this->{$prop} = $name;
        }
        return $this;
    }

    public function transformForApi(): mixed
    {
        return (object) (array) $this;
    }
}
