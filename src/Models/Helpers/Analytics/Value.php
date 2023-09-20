<?php

namespace Constellix\Client\Models\Helpers\Analytics;

use Carbon\Carbon;

/**
 * Represents a data point in an analytics data set.
 */
class Value
{
    /**
     * @var Carbon The date for this data point
     */
    public Carbon $date;

    /**
     * @var int The value for this data point
     */
    public int $value;

    /**
     * @param string $datetime Date for this point
     * @param int $value The value
     */
    public function __construct(string $datetime, int $value)
    {
        $this->date = new Carbon($datetime);
        $this->value = $value;
    }
}
