<?php

declare(strict_types=1);

namespace Constellix\Client\Managers;

use Constellix\Client\Models\AbstractModel;
use Constellix\Client\Models\VanityNameserver;

/**
 * Manages Vanity NameServer API resources.
 * @package Constellix\Client\Managers
 */
class VanityNameserverManager extends AbstractManager
{
    /**
     * The base URI for the object.
     * @var string
     */
    protected string $baseUri = '/vanitynameservers';

    public function create(): VanityNameserver
    {
        return $this->createObject();
    }
    public function get(int $id): VanityNameserver
    {
        return $this->getObject($id);
    }
}
