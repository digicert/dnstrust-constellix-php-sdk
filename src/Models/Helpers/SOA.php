<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers;

use Constellix\Client\Interfaces\Models\Helpers\ITOConfigInterface;
use Constellix\Client\Interfaces\Models\Helpers\ITOInterface;
use Constellix\Client\Interfaces\Models\Helpers\SOAInterface;
use Constellix\Client\Models\AbstractModel;
use Constellix\Client\Traits\HelperModel;

/**
 * Represents basic ITO configuration for a pool
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
class SOA extends AbstractModel implements SOAInterface
{
    use HelperModel;

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

    protected array $editable = [
        'primaryNameserver',
        'email',
        'ttl',
        'refresh',
        'retry',
        'expire',
        'negativeCache',
    ];

    public function transformForApi(): object
    {
        $payload = parent::transformForApi();
        unset($payload->serial);
        return $payload;
    }
}