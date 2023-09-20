<?php

namespace Constellix\Client\Models\Helpers\Analytics;

/**
 * Represents information about the interval for data points in the analytics result set.
 */
class Interval
{
    /**
     * @var int The maximum interval between values
     */
    public int $max;

    /**
     * @var int The minimum interval between values
     */
    public int $min;

    /**
     * @var float The mean average interval between values
     */
    public float $mean;

    /**
     * @param \stdClass $data The raw API data
     */
    public function __construct(\stdClass $data)
    {
        $this->min = $data->min;
        $this->max = $data->max;
        $this->mean = $data->mean;
    }
}
