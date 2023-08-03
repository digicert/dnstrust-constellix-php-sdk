<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers\RecordValues;

use Constellix\Client\Models\Helpers\RecordValue;

class Failover extends RecordValue
{
    public string $mode = 'normal';
    public bool $enabled = true;
    /**
     * @var array<object>
     */
    public array $values;

    public function transformForApi(): \stdClass
    {
        $payload = parent::transformForApi();
        $payload->values = array_map(function ($value) {
            return (object) [
                'value' => $value->value,
                'order' => $value->order,
                'sonarCheckId' => $value->sonarCheckId,
                'enabled' => $value->enabled,
            ];
        }, $payload->values);
        return $payload;
    }
}
