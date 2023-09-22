<?php

declare(strict_types=1);

namespace Constellix\Client\Models;

use Carbon\Carbon;
use Constellix\Client\Interfaces\Traits\DomainAwareInterface;
use Constellix\Client\Models\Helpers\Analytics\Interval;
use Constellix\Client\Models\Helpers\Analytics\Queries;
use Constellix\Client\Models\Helpers\Analytics\Stats;
use Constellix\Client\Traits\DomainAware;

/**
 * Represents Domain Analytics
 * @package Constellix\Client\Models
 *
 * @property Carbon $start
 * @property Carbon $end
 * @property Queries $queries
 * @property Interval $interval
 * @property Stats $stats
 */
class DomainAnalytics extends AbstractModel implements DomainAwareInterface
{
    use DomainAware;

    /**
     * @var array<mixed>
     */
    protected array $props = [
        'start' => null,
        'end' => null,
        'queries' => null,
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

        $this->props['queries'] = new Queries($data->queries);
    }
}
