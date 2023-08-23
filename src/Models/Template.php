<?php

declare(strict_types=1);

namespace Constellix\Client\Models;

use Constellix\Client\Exceptions\ConstellixException;
use Constellix\Client\Interfaces\Traits\EditableModelInterface;
use Constellix\Client\Interfaces\Traits\ManagedModelInterface;
use Constellix\Client\Managers\TemplateManager;
use Constellix\Client\Managers\TemplateRecordManager;
use Constellix\Client\Traits\EditableModel;
use Constellix\Client\Traits\ManagedModel;

/**
 * Represents a Template resource.
 * @package Constellix\Client\Models
 *
 * @property string $name
 * @property-read int $version
 * @property bool $geoip
 * @property bool $gtd
 * @property \DateTime $createdAt
 * @property \DateTime $updatedAt
 * @property-read TemplateRecordManager $records
 */
class Template extends AbstractModel implements EditableModelInterface, ManagedModelInterface
{
    use EditableModel;
    use ManagedModel;

    protected TemplateManager $manager;

    /**
     * @var array<mixed>
     */
    protected array $props = [
        'name' => null,
        'version' => null,
        'geoip' => null,
        'gtd' => null,
        'createdAt' => null,
        'updatedAt' => null,
    ];

    /**
     * @var string[]
     */
    protected array $editable = [
        'name',
        'geoip',
        'gtd',
    ];

    protected ?TemplateRecordManager $records = null;

    protected function parseApiData(\stdClass $data): void
    {
        parent::parseApiData($data);
        if (property_exists($data, 'createdAt')) {
            $this->props['createdAt'] = new \DateTime($data->createdAt);
        }
        if (property_exists($data, 'updatedAt')) {
            $this->props['updatedAt'] = new \DateTime($data->updatedAt);
        }
    }

    protected function getRecords(): TemplateRecordManager
    {
        if (!$this->id) {
            throw new ConstellixException('Template must be created before you can access records');
        }
        if ($this->records === null) {
            $this->records = new TemplateRecordManager($this->client);
            $this->records->setTemplate($this);
        }
        return $this->records;
    }
}
