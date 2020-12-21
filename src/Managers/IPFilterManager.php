<?php

declare(strict_types=1);

namespace Constellix\Client\Managers;

use Constellix\Client\Interfaces\Managers\IPFilterManagerInterface;
use Constellix\Client\Interfaces\Models\IPFilterInterface;

/**
 * Manages IP Filter API resources.
 * @package Constellix\Client\Managers
 */
class IPFilterManager extends AbstractManager implements IPFilterManagerInterface
{
    /**
     * The base URI for objects.
     * @var string
     */
    protected string $baseUri = '/ipfilters';

    public function create(): IPFilterInterface
    {
        return $this->createObject();
    }

    public function get(int $id): IPFilterInterface
    {
        return $this->getObject($id);
    }
}