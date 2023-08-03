<?php

declare(strict_types=1);

namespace Constellix\Client\Exceptions\Client;

use Constellix\Client\Exceptions\ConstellixException;

/**
 * Thrown when the client is unable to decode the response from the API.
 * @package Constellix\Client\Exceptions
 */
class JsonDecodeException extends ConstellixException
{
}
