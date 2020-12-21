<?php

declare(strict_types=1);

namespace Constellix\Client\Traits;

trait HelperModel
{
    public function __construct(?object $data = null)
    {
        if ($data) {
            $this->populateFromApi($data);
        }
    }

    /**
     * Returns a JSON serializable representation of the resource.
     * @return mixed|object
     * @internal
     */
    public function jsonSerialize()
    {
        $json = parent::jsonSerialize();
        unset($json->id);
        return $json;
    }
}

