<?php

namespace Oforge\Engine\Auth;

/**
 * Class AuthConstants
 *
 * @package Oforge\Engine\Auth
 */
class AuthConstants {
    public const ROLE_ANONYMOUS = 'anonymous';
    public const ROLE_USER      = 'user';

    /** Prevent instance. */
    private function __construct() {
    }

}
