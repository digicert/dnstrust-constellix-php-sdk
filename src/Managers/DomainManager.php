<?php

declare(strict_types=1);

namespace Constellix\Client\Managers;

use Constellix\Client\Models\Domain;

/**
 * Manages Domain API resources.
 * @package Constellix\Client\Managers
 */
class DomainManager extends AbstractManager
{
    /**
     * The base URI for objects.
     * @var string
     */
    protected string $baseUri = '/domains';

    public function create(): Domain
    {
        return $this->createObject();
    }

    public function get(int $id): Domain
    {
        return $this->getObject($id);
    }
}
