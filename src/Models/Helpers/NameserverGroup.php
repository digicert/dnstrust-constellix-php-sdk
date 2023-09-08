<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers;

use Constellix\Client\Interfaces\Models\Helpers\ITOConfigInterface;
use Constellix\Client\Interfaces\Models\Helpers\ITOInterface;
use Constellix\Client\Interfaces\Models\Helpers\SOAInterface;

/**
 * Represents a Nameserver Group on a Vanity Nameserver
 * @package Constellix\Client\Models
 *
 * @property ?int $id
 * @property ?string $name
 */
class NameserverGroup
{
    public int $id;
    public ?string $name = null;

    /**
     * Create a new nameserver group for a Vanity Nameserver.
     * @param \stdClass|null $data
     */
    public function __construct(?\stdClass $data = null)
    {
        if ($data !== null) {
            $this->id = $data->id ?? 1;
            $this->name = $data->name ?? null;
        }
    }

    /**
     * Transform this object and return a representation suitable for submitting to the API.
     * @return \stdClass
     * @internal
     */

    public function transformForApi(): \stdClass
    {
        return (object) [
            'id' => $this->id,
        ];
    }
}
