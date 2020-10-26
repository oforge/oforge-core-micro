<?php

namespace Oforge\Engine\Crud\Enums;

/**
 * Class CrudFilterComparator
 *
 * @package Oforge\Engine\Crud\Enums
 */
class CrudFilterComparator {
    public const EQUALS         = 'eq';
    public const NOT_EQUALS     = 'neq';
    public const LIKE           = 'like';
    public const NOT_LIKE       = 'notLike';
    public const GREATER        = 'gt';
    public const GREATER_EQUALS = 'gte';
    public const LESS           = 'lt';
    public const LESS_EQUALS    = 'lte';
    public const IS_NULL        = 'isNull';
    public const IN             = 'in';
    public const NOT_IN         = 'notIn';
    public const BETWEEN        = 'between';

    /** Prevent instance */
    private function __construct() {
    }

    public static function isValid(string $comparator) : bool {
        switch ($comparator) {
            case CrudFilterComparator::EQUALS:
            case CrudFilterComparator::NOT_EQUALS:
            case CrudFilterComparator::IS_NULL:
            case CrudFilterComparator::LIKE:
            case CrudFilterComparator::NOT_LIKE:
            case CrudFilterComparator::GREATER:
            case CrudFilterComparator::GREATER_EQUALS:
            case CrudFilterComparator::LESS:
            case CrudFilterComparator::LESS_EQUALS:
            case CrudFilterComparator::IN:
            case CrudFilterComparator::NOT_IN:
            case CrudFilterComparator::BETWEEN:
                return true;
            default:
                return false;
        }
    }

}
