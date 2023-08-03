<?php

declare(strict_types=1);

namespace Constellix\Client\Pagination\Factories;

use Constellix\Client\Interfaces\PaginatorFactoryInterface;
use Constellix\Client\Pagination\Paginator;

/**
 * Factory for Paginator objects.
 *
 * @package Constellix\Client\Pagination
 */
class PaginatorFactory implements PaginatorFactoryInterface
{
    /**
     * Returns a paginator based on the supplied items and parameters.
     *
     * @param array<object> $items
     * @param int $totalItems
     * @param int $perPage
     * @param int $currentPage
     * @return Paginator
     */
    public function paginate(array $items, int $totalItems, int $perPage, int $currentPage = 1): Paginator
    {
        return new Paginator($items, $totalItems, $perPage, $currentPage);
    }
}
