<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Traits;

use Constellix\Client\Interfaces\ClientInterface;
use Constellix\Client\Interfaces\Managers\AbstractManagerInterface;

/**
 * Trait for models which can be edited.
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
     * @param AbstractManagerInterface $manager
     * @param ClientInterface $client
     * @param object|null $data
     * @internal
     */
    public function __construct(AbstractManagerInterface $manager, ClientInterface $client, ?object $data = null);
}