<?php

declare(strict_types=1);

namespace Constellix\Client\Traits;

/**
 * Trait ConciseModel
 * @package Constellix\Client\Traits
 */
trait ConciseModel
{
    protected function getFull()
    {
        return $this->manager->get($this->id);
    }
}