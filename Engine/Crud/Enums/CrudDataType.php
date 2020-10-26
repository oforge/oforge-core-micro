<?php

namespace Oforge\Engine\Crud\Enums;

/**
 * Class CrudDataType
 *
 * @package Oforge\Engine\Crud\Enums
 */
class CrudDataType {
    public const ID     = 'ID';
    public const BOOL   = 'bool';
    public const COLOR  = 'color';
    /** For DateTimeInterface values only! */
    public const DATE = 'date';
    /** For DateTimeInterface values only! */
    public const DATETIME = 'dateTime';
    /** For DateTimeInterface values only! */
    public const TIME    = 'time';
    public const DECIMAL = 'decimal';
    public const EMAIL   = 'email';
    public const FILE    = 'file';
    public const FLOAT   = 'float';
    public const HTML    = 'html';
    public const INT     = 'int';
    public const SELECT  = 'select';
    public const TEXT    = 'text';
    public const STRING  = 'string';
    public const URL     = 'url';

    /** Prevent instance */
    private function __construct() {
    }

}
