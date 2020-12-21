<?php

declare(strict_types=1);

namespace Constellix\Client\Models;

use Constellix\Client\Interfaces\Models\VanityNameserverInterface;
use Constellix\Client\Interfaces\Traits\EditableModelInterface;
use Constellix\Client\Models\Common\CommonVanityNameserver;
use Constellix\Client\Traits\EditableModel;

/**
 * Represents a Vanity Nameserver resource.
 * @package Constellix\Client\Models
 *
 * @property string $name
 * @property bool $default
 * @property bool $public
 * @property object $nameserverGroup
 * @property string[] $nameservers;
 */
class VanityNameserver extends CommonVanityNameserver implements VanityNameserverInterface, EditableModelInterface
{
    use EditableModel;

    protected array $props = [
        'name' => null,
        'default' => null,
        'public' => null,
        'nameserverGroup' => null,
        'nameservers' => [],
    ];

    protected array $editable = [
        'name',
        'default',
        'nameserverGroup',
        'nameservers',
    ];

    protected function setInitialProperties()
    {
        $this->props['nameserverGroup'] = (object) [
            'id' => 1,
            'name' => 'NS User Group 1',
        ];
    }

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
            $this->nameservers = $nameservers;
        }
        return $this;
    }

    public function transformForApi(): object
    {
        $payload = parent::transformForApi();
        $payload->nameserverGroup = $this->nameserverGroup->id;
        return $payload;
    }
}