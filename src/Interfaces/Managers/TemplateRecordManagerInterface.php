<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Managers;

use Constellix\Client\Exceptions\Client\Http\HttpException;
use Constellix\Client\Exceptions\Client\ModelNotFoundException;
use Constellix\Client\Interfaces\Models\TemplateRecordInterface;

/**
 * Manages domain records objects from the API.
 * @package Constellix\Client\Interfaces
 */
interface TemplateRecordManagerInterface extends AbstractManagerInterface
{
    /**
     * Creates a new template record.
     * @return TemplateRecordInterface
     */
    public function create(): TemplateRecordInterface;

    /**
     * Gets the template record with the specified ID.
     * @param int $id
     * @return TemplateRecordInterface
     * @throws ModelNotFoundException
     * @throws HttpException
     */
    public function get(int $id): TemplateRecordInterface;
}