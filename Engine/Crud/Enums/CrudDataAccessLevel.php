<?php

namespace Oforge\Engine\Crud\Enums;

/**
 * Class CrudDataAccessLevel
 *
 * @package Oforge\Engine\Crud\Enums
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
