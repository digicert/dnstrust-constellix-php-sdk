<?php

declare(strict_types=1);

namespace Constellix\Client\Managers;

use Constellix\Client\Interfaces\Managers\VanityNameServerManagerInterface;
use Constellix\Client\Interfaces\Models\VanityNameserverInterface;

/**
 * Manages Vanity NameServer API resources.
 * @package Constellix\Client\Managers
 */
class VanityNameserverManager extends AbstractManager implements VanityNameServerManagerInterface
{
    /**
     * The base URI for the object.
     * @var string
     */
    protected string $baseUri = '/vanitynameservers';

    public function create(): VanityNameserverInterface
    {
        return $this->createObject();
    }

    public function get(int $id): VanityNameserverInterface
    {
        return $this->getObject($id);
    }
}