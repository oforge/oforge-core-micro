<?php

namespace PHPSTORM_META {

    if (function_exists('override')) {
        // Services
        override(\Oforge\Engine\Core\Manager\Services\ServiceManager::get(0), map([
            'auth.password'        => \Oforge\Engine\Auth\Services\PasswordService::class,
            'auth.role'            => \Oforge\Engine\Auth\Services\RoleService::class,
            'auth.role.permission' => \Oforge\Engine\Auth\Services\RolePermissionService::class,
            'auth.user'            => \Oforge\Engine\Auth\Services\UserService::class,
            'auth.user.permission' => \Oforge\Engine\Auth\Services\UserPermissionService::class,
            'auth.user.role'       => \Oforge\Engine\Auth\Services\UserRoleService::class,
            'auth.permission'      => \Oforge\Engine\Auth\Services\PermissionService::class,
            // 'auth'          => \Oforge\Engine\Auth\Services\AuthService::class,
        ]));
        // Config
        override(\Oforge\Engine\Core\Services\ConfigService::get(0), map([
            'auth_password_min_length' => 'int',
        ]));
    }

}
