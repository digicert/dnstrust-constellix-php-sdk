<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers\RecordValues;

use Constellix\Client\Models\Helpers\RecordValue;

class Failover extends RecordValue
{
    public $mode = 'normal';
    public bool $enabled = true;
    public array $values;

    public function transformForApi()
    {
        $payload = parent::transformForApi();
        $payload->values = array_map(function($value) {
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