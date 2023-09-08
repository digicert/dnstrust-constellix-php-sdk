<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers;

use Constellix\Client\Enums\Pools\ITOHandicapFactor;
use Constellix\Client\Enums\Pools\ITORegion;
use Constellix\Client\Models\AbstractModel;
use Constellix\Client\Traits\HelperModel;

/**
 * Represents ITO configuration for a pool
 * @package Constellix\Client\Models
 *
 * @property int $frequency
 * @property int $maximumNumberOfResults
 * @property int $deviationAllowance
 * @property int $period
 * @property ITORegion $monitoringRegion
 * @property ITOHandicapFactor $handicapFactor
 */
class ITOConfig extends AbstractModel
{
    use HelperModel;

    /**
     * @var array<mixed>
     */
    protected array $props = [
        'frequency' => null,
        'maximumNumberOfResults' => null,
        'deviationAllowance' => null,
        'monitoringRegion' => null,
        'handicapFactor' => null,
        'period' => null,
    ];

    /**
     * @var string[]
     */
    protected array $editable = [
        'frequency',
        'maximumNumberOfResults',
        'deviationAllowance',
        'monitoringRegion',
        'handicapFactor',
        'period',
    ];

    /**
     * Create a new ITO Config.
     * @param \stdClass|null $data
     */
    public function __construct(?\stdClass $data = null)
    {
        $this->props['monitoringRegion'] = ITORegion::WORLD();
        $this->props['handicapFactor'] = ITOHandicapFactor::NONE();
        if ($data) {
            $this->populateFromApi($data);
        }
    }


    /**
     * Parse the API response data and load it into this object.
     * @param \stdClass $data
     * @return void
     */
    protected function parseApiData(\stdClass $data): void
    {
        parent::parseApiData($data);
        if (property_exists($data, 'monitoringRegion') && $data->monitoringRegion) {
            $this->props['monitoringRegion'] = ITORegion::make($data->monitoringRegion);
        }
        if (property_exists($data, 'handicapFactor') && $data->handicapFactor) {
            $this->props['handicapFactor'] = ITOHandicapFactor::make($data->handicapFactor);
        }
    }
}
