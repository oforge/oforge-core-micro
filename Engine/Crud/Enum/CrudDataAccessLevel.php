<?php

namespace Oforge\Engine\Crud\Enum;

/**
 * Class CrudDataAccessLevel
 *
 * @package Oforge\Engine\Crud\Enum
 */
class CrudDataAccessLevel {
    public const OFF    = 0;
    public const READ   = 1;
    public const CREATE = 2;
    public const UPDATE = 3;

    /** Prevent instance */
    private function __construct() {
    }

}
