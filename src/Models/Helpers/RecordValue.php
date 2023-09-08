<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers;

/**
 * Abstract class to represents a record value
 * @package Constellix\Client\Models
 */
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
     * Parse the API data and apply any transformations to it.
     * @param \stdClass $data
     * @return \stdClass
     */
    protected function parseApiData(\stdClass $data): \stdClass
    {
        return $data;
    }

    /**
     * Populate this object with the supplied API data.
     * @param \stdClass $data
     * @return self
     * @internal
     */
    public function populateFromApi(\stdClass $data): self
    {
        $data = $this->parseApiData($data);
        foreach ((array) $data as $prop => $name) {
            $this->{$prop} = $name;
        }
        return $this;
    }


    /**
     * Transform this object and return a representation suitable for submitting to the API.
     * @return mixed
     * @internal
     */

    public function transformForApi(): mixed
    {
        return (object) (array) $this;
    }
}
