<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers\RecordValues;

use Constellix\Client\Enums\Records\FailoverMode;
use Constellix\Client\Models\Helpers\RecordValue;

class Failover extends RecordValue
{
    public FailoverMode $mode;
    public bool $enabled;
    /**
     * @var array<FailoverValue>
     */
    public array $values = [];

    public function __construct(\stdClass $data = null)
    {
        $this->mode = FailoverMode::NORMAL();
        parent::__construct($data);
    }

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

    public function transformForApi(): \stdClass
    {
        $payload = parent::transformForApi();
        $payload->mode = $this->mode->value;
        $payload->values = array_map(function ($value) {
            return $value->transformForApi();
        }, $payload->values);
        return $payload;
    }

    public function addValue(FailoverValue $value): void
    {
        if (!in_array($value, $this->values)) {
            $this->values[] = $value;
        }
    }
}
