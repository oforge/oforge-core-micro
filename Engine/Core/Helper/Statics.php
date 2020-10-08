<?php

namespace Oforge\Engine\Core\Helper;

/**
 * Class Statics
 *
 * @package Oforge\Engine\Core
 */
class Statics {
    /** Default order value for all order properties. */
    public const DEFAULT_ORDER    = 1337;
    public const GLOBAL_SEPARATOR = '/';

    public const DIR_VAR            = self::GLOBAL_SEPARATOR . 'var';
    public const DIR_CACHE          = self::DIR_VAR . self::GLOBAL_SEPARATOR . 'cache';
    public const DIR_CACHE_DB       = self::DIR_CACHE . self::GLOBAL_SEPARATOR . 'db';
    public const DIR_CACHE_ENDPOINT = self::DIR_CACHE . Statics::GLOBAL_SEPARATOR . 'endpoint';
    public const DIR_CACHE_FUNCTION = self::DIR_CACHE . self::GLOBAL_SEPARATOR . 'functions';
    public const DIR_CACHE_PROXY    = self::DIR_CACHE . self::GLOBAL_SEPARATOR . 'proxy';
    public const DIR_LOG            = self::DIR_VAR . self::GLOBAL_SEPARATOR . 'log';
    public const DIR_TMP            = self::DIR_VAR . self::GLOBAL_SEPARATOR . 'tmp';
    public const DIR_PUBLIC         = self::DIR_VAR . self::GLOBAL_SEPARATOR . 'public';
    public const DIR_UPLOAD         = self::DIR_PUBLIC . self::GLOBAL_SEPARATOR . 'upload';

    public const FILE_CACHE_DB = self::DIR_CACHE_DB . self::GLOBAL_SEPARATOR . 'db.cache';

    //
    //
    //

    public const ENGINE_DIR   = 'Engine';
    public const MODULES_DIR  = 'Modules';
    public const PLUGIN_DIR   = 'Plugins';
    // public const VIEW_DIR     = 'Views';
    public const TEMPLATE_DIR = 'Themes';
}
