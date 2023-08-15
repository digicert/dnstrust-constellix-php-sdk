<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers;

abstract class RecordValue
{
    /**
     * @param \stdClass $data
     */
    public function __construct(\stdClass $data = null)
    {
        if ($data) {
            $this->populateFromApi($data);
        }
    }

    /**
     * @param \stdClass $data
     * @return \stdClass
     */
    protected function parseApiData(\stdClass $data): \stdClass
    {
        return $data;
    }

    /**
     * @param \stdClass $data
     * @return self
     */
    public function populateFromApi(\stdClass $data): self
    {
        $data = $this->parseApiData($data);
        foreach ((array) $data as $prop => $name) {
            $this->{$prop} = $name;
        }
        return $this;
    }

    public function transformForApi(): mixed
    {
        return (object) (array) $this;
    }
}
