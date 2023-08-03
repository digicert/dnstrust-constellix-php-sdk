<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Common;

use Constellix\Client\Models\AbstractModel;
use Constellix\Client\Traits\HelperModel;

/**
 * Represents a Pool Value resource.
 * @package Constellix\Client\Models
 *
 * @property string $value
 * @property int $weight
 */
abstract class CommonPoolValue extends AbstractModel
{
    use HelperModel;

    /**
     * @var array<mixed>
     */
    protected array $props = [
        'value' => null,
        'weight' => null,
    ];

    public function __toString()
    {
        if ($this->value) {
            return $this->value;
        } else {
            return 'PoolValue';
        }
    }
}
