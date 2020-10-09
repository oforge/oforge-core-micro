<?php

namespace Oforge\Engine\Crud\Enum;

/**
 * Class CrudGroupByOrder
 *
 * @package Oforge\Engine\Crud\Enum
 */
class CrudGroupByOrder {
    public const ASC  = 'ASC';
    public const DESC = 'DESC';

    /** Prevent instance */
    private function __construct() {
    }

    /**
     * @param string $order
     *
     * @return bool
     */
    public static function isValid(string $order) {
        switch ($order) {
            case self::ASC:
            case self::DESC:
                return true;
            default:
                return false;
        }
    }

}
