<?php

namespace Oforge\Engine\Auth;

use Oforge\Engine\Auth\Models\User\User;
use Oforge\Engine\Auth\Services\AuthService;
use Oforge\Engine\Auth\Services\BackendLoginService;
use Oforge\Engine\Auth\Services\PasswordService;
use Oforge\Engine\Auth\Services\PermissionService;
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
    protected function __construct() {
        parent::__construct();
        $this->models = [
            User::class,
        ];

        $this->services = [
            'auth'          => AuthService::class,
            'backend.login' => BackendLoginService::class,
            'password'      => PasswordService::class,
            'permissions'   => PermissionService::class,
            'user'          => UserService::class
        ];
    }

    public function install() {
        parent::install();

        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');
        $configService->add([
            'name'     => 'auth_core_password_min_length',
            'type'     => ConfigType::INTEGER,
            'group'    => 'auth_core',
            'default'  => 6,
            'label'    => 'config_auth_core_password_min_length',
            'required' => true,
        ]);
    }

}
