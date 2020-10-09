<?php

namespace Oforge\Engine\Crud\Enum;

/**
 * Class CrudFilterType
 *
 * @package Oforge\Engine\Crud\Enum
 */
class CrudFilterType {
    public const HIDDEN = 'hidden';
    public const TEXT   = 'text';
    public const SELECT = 'select';
    public const RANGE  = 'range';

    /** Prevent instance */
    private function __construct() {
    }

}
