<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers\RecordValues;

use Constellix\Client\Models\Helpers\RecordValue;

class FailoverValue extends RecordValue
{
    public bool $enabled = true;
    public int $order = 1;
    public ?int $sonarCheckId = null;
    public string $value;

    public ?string $status;
    public ?bool $failed;
    public ?bool $active;

    public function transformForApi(): \stdClass
    {
        $payload = parent::transformForApi();
        unset($payload->status);
        unset($payload->failed);
        unset($payload->active);
        return $payload;
    }
}
