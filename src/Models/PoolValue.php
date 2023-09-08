<?php

declare(strict_types=1);

namespace Constellix\Client\Models;

use Constellix\Client\Enums\Pools\PoolValuePolicy;
use Constellix\Client\Traits\HelperModel;

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
class PoolValue extends AbstractModel
{
    use HelperModel;

    /**
     * @var array<mixed>
     */
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

    /**
     * @var string[]
     */
    protected array $editable = [
        'value',
        'weight',
        'handicap',
        'policy',
        'sonarCheckId',
    ];

    /**
     * Create a new Pool Value
     * @param \stdClass|null $data
     */
    public function __construct(?\stdClass $data = null)
    {
        $this->props['policy'] = PoolValuePolicy::FOLLOW_SONAR();
        $this->originalProps = $this->props;
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
        if (property_exists($data, 'policy') && $data->policy) {
            $this->props['policy'] = PoolValuePolicy::from($data->policy);
        }
    }

    /**
     * Transform this object and return a representation suitable for submitting to the API.
     * @return \stdClass
     */
    public function transformForApi(): \stdClass
    {
        $payload = parent::transformForApi();

        foreach ((array) $payload as $propName => $value) {
            if (!in_array($propName, $this->editable)) {
                unset($payload->{$propName});
            }
        }

        return $payload;
    }

    /**
     * Return the string representation of this pool value
     * @return string
     * @internal
     */
    public function __toString()
    {
        if ($this->value) {
            return $this->value;
        } else {
            return 'PoolValue';
        }
    }
}
