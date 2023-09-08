<?php

declare(strict_types=1);

namespace Constellix\Client\Traits;

trait HelperModel
{
    public function __construct(?\stdClass $data = null)
    {
        if ($data !== null) {
            $this->populateFromApi($data);
        }
    }

    /**
     * Returns a JSON serializable representation of the resource.
     * @return \stdClass
     * @internal
     */
    public function jsonSerialize(): \stdClass
    {
        $json = parent::jsonSerialize();
        unset($json->id);
        return $json;
    }
}
