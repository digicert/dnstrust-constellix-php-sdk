<?php

declare(strict_types=1);

namespace Constellix\Client\Managers;

use Constellix\Client\Models\IPFilter;

/**
 * Manages IP Filter API resources.
 * @package Constellix\Client\Managers
 */
class IPFilterManager extends AbstractManager
{
    /**
     * The base URI for objects.
     * @var string
     */
    protected string $baseUri = '/ipfilters';

    public function create(): IPFilter
    {
        return $this->createObject();
    }

    public function get(int $id): IPFilter
    {
        return $this->getObject($id);
    }
}
