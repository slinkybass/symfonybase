<?php

namespace App\Repository\Filter;

/**
 * Comparison operators for `AbstractFilter::applyComparison()` / `applyMultiComparison()`.
 *
 * `BETWEEN` expects `$value` as a two-element array; `IN`/`NOT_IN` expect an array of scalars.
 */
enum ComparisonOperator
{
    // String comparisons
    case LIKE;
    case NOT_LIKE;
    case STARTS_WITH;
    case ENDS_WITH;

    // Equality
    case EQ;
    case NEQ;

    // Collections
    case IN;
    case NOT_IN;

    // Numeric / date comparisons
    case GT;
    case GTE;
    case LT;
    case LTE;
    case BETWEEN;

    // Null checks
    case IS_NULL;
    case IS_NOT_NULL;
}
