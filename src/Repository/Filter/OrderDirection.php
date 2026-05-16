<?php

namespace App\Repository\Filter;

/**
 * Sort direction passed to `OrderFilter` (bound as the SQL keyword string).
 */
enum OrderDirection: string
{
    case ASC = 'ASC';
    case DESC = 'DESC';
}
