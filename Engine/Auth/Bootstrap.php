<?php

namespace Oforge\Engine\Auth;

use Oforge\Engine\Auth\Services\RoleService;
use Oforge\Engine\Auth\Services\UserService;
use Oforge\Engine\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Core\Models\Config\ConfigType;
use Oforge\Engine\Core\Services\ConfigService;

/**
 * Class Bootstrap
 *
 * @package Oforge\Engine\Auth
 */
class Bootstrap extends AbstractBootstrap {

    /**
     * Bootstrap constructor.
     */
    public function __construct() {
        $this->middlewares = [// Middlewares\SecureEndpointMiddleware::class
        ];

        $this->models = [
            Models\Permission::class,
            Models\Role::class,
            Models\RolePermission::class,
            Models\User::class,
            Models\UserPermission::class,
            Models\UserRole::class,
        ];

        $this->services = [
            'auth.login'           => Services\LoginService::class,
            'auth.role'            => Services\RoleService::class,
            'auth.role.permission' => Services\RolePermissionService::class,
            'auth.user'            => Services\UserService::class,
            'auth.user.permission' => Services\UserPermissionService::class,
            'auth.user.role'       => Services\UserRoleService::class,
            'auth.password'        => Services\PasswordService::class,
            'auth.permission'      => Services\PermissionService::class,
            // 'auth'          => AuthService::class,
        ];
    }

    /** @inheritdoc */
    public function install() {
        parent::install();
        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');
        $configService->add([
            'name'     => 'auth_password_min_length',
            'type'     => ConfigType::INTEGER,
            'group'    => 'auth',
            'default'  => 6,
            'label'    => 'config_auth_password_min_length',
            'required' => true,
        ]);
        /** @var RoleService $roleService */
        $roleService = Oforge()->Services()->get('auth.role');
        $roleService->installDefaultRoles();


    }

}
