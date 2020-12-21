<?php

declare(strict_types=1);

namespace Constellix\Client\Exceptions\Client;

use Constellix\Client\Exceptions\ConstellixException;

/**
 * Exception thrown when an attempt is made to set a read-only property on an API resource.
 * @package Constellix\Client\Exceptions
 */
class ReadOnlyPropertyException extends ConstellixException
{

}