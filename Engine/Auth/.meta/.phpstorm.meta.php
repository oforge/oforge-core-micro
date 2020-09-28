<?php

namespace PHPSTORM_META {

    if (function_exists('override')) {
        override(\Oforge\Engine\Core\Manager\Services\ServiceManager::get(0), map([
            'auth'          => \Oforge\Engine\Auth\Services\AuthService::class,
            'backend.login' => \Oforge\Engine\Auth\Services\BackendLoginService::class,
            'password'      => \Oforge\Engine\Auth\Services\PasswordService::class,
            'permissions'   => \Oforge\Engine\Auth\Services\PermissionService::class,
        ]));

        override(\Oforge\Engine\Core\Services\ConfigService::get(0), map([
            'auth_core_password_min_length' => 'int',
        ]));

    }

}
