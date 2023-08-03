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
    ];

    public function __construct(?\stdClass $data = null)
    {
        $this->props['monitoringRegion'] = ITORegion::world();
        $this->props['handicapFactor'] = ITOHandicapFactor::none();
        if ($data) {
            $this->populateFromApi($data);
        }
    }

    protected function parseApiData(object $data): void
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
