<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers\RecordValues;

use Constellix\Client\Enums\Records\FailoverMode;
use Constellix\Client\Models\Helpers\RecordValue;

/**
 * Represents the data in failover mode for A, AAAA, CNAME and ANAME records.
 * @package Constellix\Client\Models\RecordValues
 */
class Failover extends RecordValue
{
    public FailoverMode $mode;
    public bool $enabled;
    /**
     * @var array<FailoverValue>
     */
    public array $values = [];

    /**
     * Construct a new Failover record value
     * @param \stdClass|null $data
     */
    public function __construct(\stdClass $data = null)
    {
        $this->mode = FailoverMode::NORMAL();
        parent::__construct($data);
    }

    /**
     * Parse the API response data and load it into this object.
     * @param \stdClass $data
     * @return \stdClass
     */
    public function parseApiData(\stdClass $data): \stdClass
    {
        if (property_exists($data, 'mode')) {
            $data->mode = FailoverMode::from($data->mode);
        }
        if (property_exists($data, 'values')) {
            $data->values = array_map(function ($value) {
                return new FailoverValue($value);
            }, $data->values);
        }
        return parent::parseApiData($data);
    }

    /**
     * Transform this object and return a representation suitable for submitting to the API.
     * @return \stdClass
     * @internal
     */
    public function transformForApi(): \stdClass
    {
        $payload = parent::transformForApi();
        $payload->mode = $this->mode->value;
        $payload->values = array_map(function ($value) {
            return $value->transformForApi();
        }, $payload->values);
        return $payload;
    }

    /**
     * Add a failover value to this failover record.
     * @param FailoverValue $value
     * @return void
     */
    public function addValue(FailoverValue $value): void
    {
        if (!in_array($value, $this->values)) {
            $this->values[] = $value;
        }
    }
}
