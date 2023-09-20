<?php

namespace Constellix\Client\Models\Helpers\Analytics;

/**
 * Represents statistics about an analytics data set.
 */
class Stats
{
    /**
     * @var int The total sum of all values
     */
    public int $sum;

    /**
     * @var int The maximum value
     */
    public int $max;

    /**
     * @var int The minimum value
     */
    public int $min;

    /**
     * @var float The mean average of all values
     */
    public float $mean;

    /**
     * @var int The count of all data points
     */
    public int $count;

    /**
     * @param \stdClass $data The raw API data
     */
    public function __construct(\stdClass $data)
    {
        $this->sum = $data->sum;
        $this->min = $data->min;
        $this->max = $data->max;
        $this->mean = $data->mean;
        $this->count = $data->count;
    }
}
