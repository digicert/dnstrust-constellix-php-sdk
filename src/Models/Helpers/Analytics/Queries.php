<?php

namespace Constellix\Client\Models\Helpers\Analytics;

/**
 * Represents query data for the analytics
 */
class Queries
{
    /**
     * @var ?QueryType GeoProximity query data
     */
    public ?QueryType $geoProximity = null;

    /**
     * @var ?QueryType Standard query data
     */
    public ?QueryType $standard = null;

    /**
     * @var ?QueryType Geofilter query data
     */
    public ?QueryType $geoFilter = null;

    /**
     * @param array<mixed> $data The raw API data
     */
    public function __construct(array $data)
    {
        $lookup = [
            'geo_filter' => 'geoFilter',
            'geo_proximity' => 'geoProximity',
            'standard' => 'standard',
        ];
        foreach ($data as $values) {
            if (!array_key_exists($values->type, $lookup)) {
                continue;
            }
            $prop = $lookup[$values->type];

            $this->{$prop} = new QueryType($values);
        }
    }
}
