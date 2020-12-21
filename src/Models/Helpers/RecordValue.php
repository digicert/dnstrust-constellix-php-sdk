<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers;

abstract class RecordValue
{
    public function __construct($data = null)
    {
        if ($data) {
            $this->populateFromApi($data);
        }
    }

    protected function parseApiData(object $data)
    {
        return $data;
    }

    public function populateFromApi(object $data): self
    {
        $data = $this->parseApiData($data);
        foreach ($data as $prop => $name)
        {
            $this->{$prop} = $name;
        }
        return $this;
    }

    public function transformForApi()
    {
        return (object) (array) $this;
    }
}