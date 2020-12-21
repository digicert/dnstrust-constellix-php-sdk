<?php

declare(strict_types=1);

namespace Constellix\Client\Interfaces\Managers;

use Constellix\Client\Exceptions\Client\Http\HttpException;
use Constellix\Client\Exceptions\Client\ModelNotFoundException;
use Constellix\Client\Interfaces\Models\TemplateInterface;

/**
 * Manages Template objects from the API.
 * @package Constellix\Client\Interfaces
 */
interface TemplateManagerInterface extends AbstractManagerInterface
{
    /**
     * Creates a new Template.
     * @return TemplateInterface
     */
    public function create(): TemplateInterface;

    /**
     * Gets the Template with the specified ID.
     * @param int $id
     * @return TemplateInterface
     * @throws ModelNotFoundException
     * @throws HttpException
     */
    public function get(int $id): TemplateInterface;
}