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
 * @property string[] $nameservers
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

    /**
     * Set our initial properties.
     * @return void
     */

    protected function setInitialProperties(): void
    {
        $this->props['nameserverGroup'] = new NameserverGroup((object) [
            'id' => 1,
            'name' => 'NS User Group 1',
        ]);
    }

    /**
     * Add a nameserver to the Vanity Nameserver.
     * @param string $nameserver
     * @return $this
     */
    public function addNameServer(string $nameserver): self
    {
        $this->addToCollection('nameservers', $nameserver);
        return $this;
    }

    /**
     * Remove a nameserver from the Vanity Nameserver.
     * @param string $nameserver
     * @return $this
     */
    public function removeNameServer(string $nameserver): self
    {
        $this->removeFromCollection('nameservers', $nameserver);
        return $this;
    }

    /**
     * Parse the API response data and load it into this object.
     * @param \stdClass $data
     * @return void
     * @internal
     */
    public function parseApiData(\stdClass $data): void
    {
        parent::parseApiData($data);
        if (property_exists($data, 'nameserverGroup')) {
            $this->nameserverGroup = new NameserverGroup($data->nameserverGroup);
        }
    }

    /**
     *
     * Transform this object and return a representation suitable for submitting to the API.
     * @return \stdClass
     * @internal
     */
    public function transformForApi(): \stdClass
    {
        $payload = parent::transformForApi();
        $payload->nameserverGroup = $this->nameserverGroup->transformForApi();
        return $payload;
    }
}
