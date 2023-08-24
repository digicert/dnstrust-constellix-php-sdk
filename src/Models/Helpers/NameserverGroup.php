<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers;

use Constellix\Client\Interfaces\Models\Helpers\ITOConfigInterface;
use Constellix\Client\Interfaces\Models\Helpers\ITOInterface;
use Constellix\Client\Interfaces\Models\Helpers\SOAInterface;
use Constellix\Client\Models\AbstractModel;
use Constellix\Client\Traits\HelperModel;

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

    public function __construct(?\stdClass $data = null)
    {
        if ($data !== null) {
            $this->id = $data->id ?? 1;
            $this->name = $data->name ?? null;
        }
    }

    public function transformForApi(): \stdClass
    {
        return (object) [
            'id' => $this->id,
        ];
    }
}
