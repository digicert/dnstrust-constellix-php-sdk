<?php

declare(strict_types=1);

namespace Constellix\Client\Models;

use Carbon\Carbon;
use Constellix\Client\Managers\AnalyticsManager;
use Constellix\Client\Models\Helpers\Analytics\Interval;
use Constellix\Client\Models\Helpers\Analytics\Stats;
use Constellix\Client\Models\Helpers\Analytics\Value;
use Constellix\Client\Traits\ManagedModel;

/**
 * Represents Account Analytics
 * @package Constellix\Client\Models
 *
 * @property Carbon $start
 * @property Carbon $end
 * @property Value[] $values
 * @property Interval $interval
 * @property Stats $stats
 */
class Analytics extends AbstractModel
{
    use ManagedModel;

    protected AnalyticsManager $manager;

    /**
     * @var array<mixed>
     */
    protected array $props = [
        'start' => null,
        'end' => null,
        'values' => [],
        'interval' => null,
        'stats' => null,
    ];

    /**
     * Parse the API response data and load it into this object.
     * @param \stdClass $data
     * @return void
     */
    protected function parseApiData(\stdClass $data): void
    {
        $this->props['start'] = new Carbon($data->start);
        $this->props['end'] = new Carbon($data->end);

        $this->id = (int)($this->start->format('Ymd') . $this->end->format('Ymd'));

        $this->props['interval'] = new Interval($data->interval);
        $this->props['stats'] = new Stats($data->stats);

        $this->props['values'] = array_map(function ($datapoint) {
            return new Value($datapoint->date, $datapoint->value);
        }, $data->values);
    }
}
