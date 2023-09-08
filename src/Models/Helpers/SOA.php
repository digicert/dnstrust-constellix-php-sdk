<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers;

use Constellix\Client\Interfaces\Models\Helpers\ITOConfigInterface;
use Constellix\Client\Interfaces\Models\Helpers\ITOInterface;
use Constellix\Client\Interfaces\Models\Helpers\SOAInterface;
use Constellix\Client\Models\AbstractModel;
use Constellix\Client\Traits\HelperModel;

/**
 * Represents SOA configuration for a domain
 * @package Constellix\Client\Models
 *
 * @property string $primaryNameserver
 * @property string $email
 * @property int $ttl
 * @property-read int $serial
 * @property int $refresh
 * @property int $retry
 * @property int $expire
 * @property int $negativeCache
 */
class SOA extends AbstractModel
{
    use HelperModel;

    /**
     * @var array<mixed>
     */
    protected array $props = [
        'primaryNameserver' => null,
        'email' => null,
        'ttl' => null,
        'serial' => null,
        'refresh' => null,
        'retry' => null,
        'expire' => null,
        'negativeCache' => null,
    ];

    /**
     * @var string[]
     */
    protected array $editable = [
        'primaryNameserver',
        'email',
        'ttl',
        'refresh',
        'retry',
        'expire',
        'negativeCache',
    ];


    /**
     * Transform this object and return a representation suitable for submitting to the API.
     * @return \stdClass
     * @internal
     */

    public function transformForApi(): \stdClass
    {
        $payload = parent::transformForApi();
        unset($payload->serial);
        return $payload;
    }
}
