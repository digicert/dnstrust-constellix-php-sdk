<?php

declare(strict_types=1);

namespace Constellix\Client\Models;

use Constellix\Client\Interfaces\Traits\EditableModelInterface;
use Constellix\Client\Managers\VanityNameserverManager;
use Constellix\Client\Models\Helpers\NameserverGroup;
use Constellix\Client\Traits\EditableModel;
use Constellix\Client\Traits\ManagedModel;

/**
 * Represents a Vanity Nameserver resource.
 * @package Constellix\Client\Models
 *
 * @property string $name
 * @property bool $default
 * @property bool $public
 * @property NameserverGroup $nameserverGroup
 * @property string[] $nameservers;
 */
class VanityNameserver extends AbstractModel implements EditableModelInterface
{
    use EditableModel;
    use ManagedModel;

    protected VanityNameserverManager $manager;

    /**
     * @var array<mixed>
     */
    protected array $props = [
        'name' => null,
        'default' => null,
        'public' => null,
        'nameserverGroup' => null,
        'nameservers' => [],
    ];

    /**
     * @var string[]
     */
    protected array $editable = [
        'name',
        'default',
        'nameserverGroup',
        'nameservers',
    ];

    protected function setInitialProperties(): void
    {
        $this->props['nameserverGroup'] = new NameserverGroup((object) [
            'id' => 1,
            'name' => 'NS User Group 1',
        ]);
    }

    /**
     * @param string $nameserver
     */
    public function addNameServer(string $nameserver): self
    {
        if (!in_array($nameserver, $this->nameservers)) {
            $nameservers = $this->nameservers;
            $nameservers[] = $nameserver;
            $this->nameservers = $nameservers;
        }
        return $this;
    }

    public function removeNameServer(string $nameserver): self
    {
        $index = array_search($nameserver, $this->nameservers);
        if ($index !== false) {
            $nameservers = $this->nameservers;
            unset($nameservers[$index]);
            $this->nameservers = array_values($nameservers);
        }
        return $this;
    }

    public function parseApiData(\stdClass $data): void
    {
        parent::parseApiData($data);
        if (property_exists($data, 'nameserverGroup')) {
            $this->nameserverGroup = new NameserverGroup($data->nameserverGroup);
        }
    }

    public function transformForApi(): \stdClass
    {
        $payload = parent::transformForApi();
        $payload->nameserverGroup = $this->nameserverGroup->transformForApi();
        return $payload;
    }
}
