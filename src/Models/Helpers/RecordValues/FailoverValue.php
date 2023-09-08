<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers\RecordValues;

use Constellix\Client\Models\Helpers\RecordValue;

/**
 * Represents the values of failover mode A, AAAA, CNAME and ANAME records.
 * @package Constellix\Client\Models\RecordValues
 */
class FailoverValue extends RecordValue
{
    public bool $enabled = true;
    public int $order = 1;
    public ?int $sonarCheckId = null;
    public string $value;

    public ?string $status;
    public ?bool $failed;
    public ?bool $active;


    /**
     * Transform this object and return a representation suitable for submitting to the API.
     * @return \stdClass
     * @internal
     */
    public function transformForApi(): \stdClass
    {
        $payload = parent::transformForApi();
        unset($payload->status);
        unset($payload->failed);
        unset($payload->active);
        return $payload;
    }
}
