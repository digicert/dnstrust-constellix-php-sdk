<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Traits;

use Constellix\Client\Client;
use Constellix\Client\Managers\AbstractManager;

/**
 * Interface models which can be edited.
 *
 * @package Constellix\Client\Interfaces
 *
 * @property-read int $id
 */
interface ManagedModelInterface
{
    /**
     * Creates a new model of the resource
     *
     * @param AbstractManager $manager
     * @param Client $client
     * @param ?\stdClass $data
     * @internal
     */
    public function __construct(AbstractManager $manager, Client $client, ?\stdClass $data = null);
}
