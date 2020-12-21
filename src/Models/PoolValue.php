<?php

declare(strict_types=1);

namespace Constellix\Client\Models;

use Constellix\Client\Enums\Pools\PoolValuePolicy;
use Constellix\Client\Interfaces\Models\PoolValueInterface;
use Constellix\Client\Models\Common\CommonPoolValue;

/**
 * Represents a value for a Pool
 * @package Constellix\Client\Models
 *
 * @property string $value
 * @property int $weight
 * @property-read bool $enabled
 * @property float $handicap
 * @property PoolValuePolicy $policy
 * @property int $sonarCheckId
 * @property-read bool $activated
 * @property-read bool $failed
 * @property-read float $speed
 */
class PoolValue extends CommonPoolValue implements PoolValueInterface
{

    protected array $props = [
        'value' => null,
        'weight' => 10,
        'enabled' => true,
        'handicap' => null,
        'policy' => null,
        'sonarCheckId' => null,
        'activated' => null,
        'failed' => null,
        'speed' => null,
    ];

    protected array $editable = [
        'value',
        'weight',
        'handicap',
        'policy',
        'sonarCheckId',
    ];

    public function __construct(?object $data = null)
    {
        $this->props['policy'] = PoolValuePolicy::FOLLOW_SONAR();
        $this->originalProps = $this->props;
        if ($data) {
            $this->populateFromApi($data);
        }
    }

    protected function parseApiData(object $data): void
    {
        parent::parseApiData($data);
        if (property_exists($data, 'policy') && $data->policy) {
            $this->props['policy'] = PoolValuePolicy::make($data->policy);
        }
    }

    public function transformForApi(): object
    {
        $payload = parent::transformForApi();

        foreach ($payload as $propName => $value) {
            if (!in_array($propName, $this->editable)) {
                unset($payload->{$propName});
            }
        }

        return $payload;
    }
}