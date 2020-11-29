<?php

namespace Oforge\Engine\Auth\Helper;

/**
 * Class UserPermissionHelper
 *
 * @package Oforge\Engine\Auth\Helper
 */
class UserPermissionHelper {

    /** Prevent instance. */
    private function __construct() {
    }

    /**
     * @param string $permission
     * @param bool $default
     *
     * @return bool
     */
    public static function has(string $permission, bool $default = false) : bool {
        $userData = $_SESSION['user'] ?? Oforge()->View()->get('user', []);

        return $userData['permissions'][$permission] ?? $default;
    }

}
