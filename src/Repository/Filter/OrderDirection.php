<?php

namespace App\Repository\Filter;

/**
 * Defines the sort directions available for query filters.
 */
enum OrderDirection: string
{
    case ASC = 'ASC';
    case DESC = 'DESC';
}
