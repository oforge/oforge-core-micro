<?php

namespace Oforge\Engine\Auth\Helper;

/**
 * Class UserRoleHelper
 *
 * @package Oforge\Engine\Auth\Helper
 */
class UserRoleHelper {

    /** Prevent instance. */
    private function __construct() {
    }

    /**
     * @param string $role
     *
     * @return bool
     */
    public static function has(string $role) : bool {
        $userData = $_SESSION['user'] ?? Oforge()->View()->get('user', []);

        return $userData['roles'][$role] ?? false;
    }

}
