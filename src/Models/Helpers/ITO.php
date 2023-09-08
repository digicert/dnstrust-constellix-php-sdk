<?php

declare(strict_types=1);

namespace Constellix\Client\Models\Helpers;

use Constellix\Client\Models\AbstractModel;
use Constellix\Client\Traits\HelperModel;

/**
 * Represents basic ITO configuration for a pool
 * @package Constellix\Client\Models
 *
 * @property ?bool $enabled
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

    /**
     * Create a new ITO object to represent ITO configuration.
     * @param \stdClass|null $data
     */
    public function __construct(?\stdClass $data = null)
    {
        $this->props['config'] = new ITOConfig();
        $this->originalProps = $this->props;
        if ($data) {
            $this->populateFromApi($data);
        }
    }

    /**
     * Parse the API response data and load it into this object.
     * @param \stdClass $data
     * @return void
     */
    protected function parseApiData(\stdClass $data): void
    {
        parent::parseApiData($data);
        if (property_exists($data, 'config') && $data->config) {
            $this->props['config'] = new ITOConfig($data->config);
        } else {
            $this->props['config'] = new ITOConfig();
        }
    }

    /**
     * Transform this object and return a representation suitable for submitting to the API.
     * @return \stdClass
     * @internal
     */
    public function transformForApi(): \stdClass
    {
        return (object) [
            'enabled' => (bool)$this->enabled,
            'config' => $this->config->transformForApi(),
        ];
    }
}
