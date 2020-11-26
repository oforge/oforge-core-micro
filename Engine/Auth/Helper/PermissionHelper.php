<?php

namespace Oforge\Engine\Auth\Helper;

/**
 * Class PermissionHelper
 *
 * @package Oforge\Engine\Auth\Helper
 */
class PermissionHelper {

    /** Prevent instance. */
    private function __construct() {
    }

    /**
     * @param array<string,bool> $permissions
     * @param string $permission
     *
     * @return bool
     */
    public static function has(array $permissions, string $permission) : bool {
        return $permissions[$permission] ?? false;
    }

}
