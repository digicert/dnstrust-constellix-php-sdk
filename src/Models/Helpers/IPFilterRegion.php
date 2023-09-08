<?php

namespace Constellix\Client\Models\Helpers;

use Constellix\Client\Enums\Continent;
use Constellix\Client\Models\AbstractModel;
use Constellix\Client\Traits\HelperModel;

/**
 * Represents IP Filter Region configuration.
 * @package Constellix\Client\Models
 *
 * @property ?Continent $continent
 * @property ?string $country
 * @property ?string $region
 * @property ?int $asn
 */
class IPFilterRegion extends AbstractModel
{
    use HelperModel;

    /**
     * @var array<mixed>
     */
    protected array $props = [
        'continent' => null,
        'country' => null,
        'region' => null,
        'asn' => null,
    ];

    /**
     * @var string[]
     */
    protected array $editable = [
        'continent',
        'country',
        'region',
        'asn',
    ];


    /**
     * Parse the API response data and load it into this object.
     * @param \stdClass $data
     * @return void
     */
    protected function parseApiData(object $data): void
    {
        parent::parseApiData($data);
        if (property_exists($data, 'continent') && $data->continent) {
            $this->continent = Continent::from($data->continent);
        }
    }
}
