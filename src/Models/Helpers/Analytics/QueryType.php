<?php

namespace Constellix\Client\Models\Helpers\Analytics;

/**
 * Represents query data and statistics for a particular query type
 */
class QueryType
{
    /**
     * @var Stats The statistics for this query type
     */
    public Stats $stats;

    /**
     * @var Value[] The values for this query type
     */
    public array $values;

    /**
     * @param \stdClass $data The raw API data
     */
    public function __construct(\stdClass $data)
    {
        $this->stats = new Stats($data->stats);
        $this->values = array_map(function ($dataPoint) {
            return new Value($dataPoint->date, $dataPoint->value);
        }, $data->values);
    }
}
