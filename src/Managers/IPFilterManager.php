<?php

declare(strict_types=1);

namespace Constellix\Client\Managers;

use Constellix\Client\Models\IPFilter;
use Constellix\Client\Traits\HasPagination;

/**
 * Manages IP Filter API resources.
 * @package Constellix\Client\Managers
 */
class IPFilterManager extends AbstractManager
{
    use HasPagination;

    /**
     * The base URI for objects.
     * @var string
     */
    protected string $baseUri = '/ipfilters';

    /**
     * Create a new IP Filter.
     * @return IPFilter
     */
    public function create(): IPFilter
    {
        return $this->createObject();
    }

    /**
     * Fetch an existing IP Filter.
     * @param int $id
     * @return IPFilter
     * @throws \Constellix\Client\Exceptions\Client\Http\HttpException
     * @throws \Constellix\Client\Exceptions\Client\ModelNotFoundException
     * @throws \ReflectionException
     */

    public function get(int $id): IPFilter
    {
        return $this->getObject($id);
    }
}
