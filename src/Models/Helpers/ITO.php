<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers;

use Constellix\Client\Models\AbstractModel;
use Constellix\Client\Traits\HelperModel;

/**
 * Represents basic ITO configuration for a pool
 * @package Constellix\Client\Models
 *
 * @property bool $enabled
 * @property ITOConfig $config
 */
class ITO extends AbstractModel
{
    use HelperModel;

    /**
     * @var array<mixed>
     */
    protected array $props = [
        'enabled' => null,
        'config' => null,
    ];

    /**
     * @var string[]
     */
    protected array $editable = [
        'enabled',
        'config',
    ];

    public function __construct(?\stdClass $data = null)
    {
        $this->props['config'] = new ITOConfig();
        $this->originalProps = $this->props;
        if ($data) {
            $this->populateFromApi($data);
        }
    }

    protected function parseApiData(object $data): void
    {
        parent::parseApiData($data);
        if (property_exists($data, 'config') && $data->config) {
            $this->props['config'] = new ITOConfig($data->config);
        } else {
            $this->props['config'] = new ITOConfig();
        }
    }

    public function transformForApi(): \stdClass
    {
        $payload = parent::transformForApi();
        $payload->config = $this->config->transformForApi();
        return $payload;
    }
}
