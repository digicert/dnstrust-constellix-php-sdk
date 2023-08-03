<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers\RecordValues;

use Constellix\Client\Models\Helpers\RecordValue;

class RoundRobinFailover extends RecordValue
{
    public string $value;
    public int $order;
    public ?int $sonarCheckId;
    public bool $enabled = true;
    public ?bool $active;
    public ?bool $failed;
    public string $status;

    public function transformForApi(): \stdClass
    {
        $payload = parent::transformForApi();
        unset(
            $payload->active,
            $payload->failed,
            $payload->status,
        );
        return $payload;
    }
}
