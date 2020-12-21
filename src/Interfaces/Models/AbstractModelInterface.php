<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Models;

use Constellix\Client\Interfaces\ClientInterface;
use Constellix\Client\Interfaces\Managers\AbstractManagerInterface;

/**
 * Represents a resource from the Constellix API.
 *
 * @package Constellix\Client\Interfaces
 *
 * @property-read int $id
 */
interface AbstractModelInterface
{

    /**
     * Populate the object from API data.
     *
     * @param object $data
     * @param bool $parse
     * @internal
     */
    public function populateFromApi(object $data, bool $parse = false): void;

    /**
     * Generate a representation of the object for sending to the API.
     *
     * @return object
     * @internal
     */
    public function transformForApi(): object;
}