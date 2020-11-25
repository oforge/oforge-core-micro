<?php

namespace PHPSTORM_META {

    if (function_exists('override')) {
        // Services
        override(\Oforge\Engine\Core\Manager\Services\ServiceManager::get(0), map([
            'auth.password'  => \Oforge\Engine\Auth\Services\PasswordService::class,
            'auth.role'      => \Oforge\Engine\Auth\Services\RoleService::class,
            'auth.user'      => \Oforge\Engine\Auth\Services\UserService::class,
            'auth.user_role' => \Oforge\Engine\Auth\Services\UserRoleService::class,
            // 'auth'          => \Oforge\Engine\Auth\Services\AuthService::class,
            // 'permissions'   => \Oforge\Engine\Auth\Services\PermissionService::class,
        ]));
        // Config
        override(\Oforge\Engine\Core\Services\ConfigService::get(0), map([
            'auth_password_min_length' => 'int',
        ]));
    }

}
