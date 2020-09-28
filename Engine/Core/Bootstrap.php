<?php

namespace Oforge\Engine\Core;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Core\Controller\Frontend\NotFoundController;
use Oforge\Engine\Core\Controller\Frontend\PingController;
use Oforge\Engine\Core\Controller\Frontend\ServerErrorController;
use Oforge\Engine\Core\Models\Config\Config;
use Oforge\Engine\Core\Models\Config\ConfigType;
use Oforge\Engine\Core\Models\Config\Value;
use Oforge\Engine\Core\Models\Endpoint\Endpoint;
use Oforge\Engine\Core\Models\Event\EventModel;
use Oforge\Engine\Core\Models\Module\Module;
use Oforge\Engine\Core\Models\Plugin\Middleware;
use Oforge\Engine\Core\Models\Plugin\Plugin;
use Oforge\Engine\Core\Models\Store\KeyValue;
use Oforge\Engine\Core\Services\ConfigService;
use Oforge\Engine\Core\Services\EncryptionService;
use Oforge\Engine\Core\Services\EndpointService;
use Oforge\Engine\Core\Services\KeyValueStoreService;
use Oforge\Engine\Core\Services\MiddlewareService;
use Oforge\Engine\Core\Services\PingService;
use Oforge\Engine\Core\Services\PluginAccessService;
use Oforge\Engine\Core\Services\PluginStateService;
use Oforge\Engine\Core\Services\RedirectService;
use Oforge\Engine\Core\Services\Session\SessionManagementService;
use Oforge\Engine\Core\Services\TokenService;

/**
 * Class Core-Bootstrap
 *
 * @package Oforge\Engine\Core
 */
class Bootstrap extends AbstractBootstrap {

    public function __construct() {
        $this->endpoints = [
            PingController::class,
            NotFoundController::class,
            ServerErrorController::class,
        ];

        $this->models = [
            Config::class,
            Endpoint::class,
            EventModel::class,
            KeyValue::class,
            Middleware::class,
            Module::class,
            Plugin::class,
            Value::class,
        ];

        $this->services = [
            'config'             => ConfigService::class,
            'encryption'         => EncryptionService::class,
            'endpoint'           => EndpointService::class,
            'middleware'         => MiddlewareService::class,
            'ping'               => PingService::class,
            'plugin.access'      => PluginAccessService::class,
            'plugin.state'       => PluginStateService::class,
            'redirect'           => RedirectService::class,
            'session.management' => SessionManagementService::class,
            'store.keyvalue'     => KeyValueStoreService::class,
            'token'              => TokenService::class,
        ];

        $this->order = 0;
    }

    /**
     * @throws Exceptions\ConfigOptionKeyNotExistException
     * @throws Exceptions\ServiceNotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function install() {
        // TODO: translations won't work here, since this module is pre-loaded before anything else

        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');

        $configService->add([
            'name'     => 'system_project_name',
            'type'     => ConfigType::STRING,
            'group'    => 'system',
            'default'  => 'Oforge',
            'label'    => 'config_system_project_name',
            'required' => true,
        ]);
        $configService->add([
            'name'     => 'system_project_short',
            'type'     => ConfigType::STRING,
            'group'    => 'system',
            'default'  => 'OF',
            'label'    => 'config_system_project_short',
            'required' => true,
        ]);
        $configService->add([
            'name'     => 'system_project_copyright',
            'type'     => ConfigType::STRING,
            'group'    => 'system',
            'default'  => 'Oforge',
            'label'    => 'config_system_project_copyright',
            'required' => true,
        ]);
        $configService->add([
            'name'     => 'system_project_domain_name',
            'type'     => ConfigType::STRING,
            'group'    => 'system',
            'default'  => '',
            'label'    => 'config_system_domain_name',
            'required' => true,
        ]);
        $configService->add([
            'name'    => 'debug_mode',
            'type'    => ConfigType::BOOLEAN,
            'group'   => 'debug',
            'default' => false,
            'label'   => 'config_debug_mode',
        ]);
        $configService->add([
            'name'    => 'debug_console',
            'type'    => ConfigType::BOOLEAN,
            'group'   => 'debug',
            'default' => true,
            'label'   => 'debug_console',
        ]);
        $configService->add([
            'name'    => 'debug_session',
            'type'    => ConfigType::BOOLEAN,
            'group'   => 'debug',
            'default' => true,
            'label'   => 'config_debug_session',
        ]);
        $configService->add([
            'name'    => 'css_source_map',
            'type'    => ConfigType::BOOLEAN,
            'group'   => 'debug',
            'default' => true,
            'label'   => 'config_css_source_map',
        ]);
        $configService->add([
            'name'     => 'system_format_datetime',
            'type'     => ConfigType::STRING,
            'group'    => 'date_format',
            'default'  => 'd.m.Y H:i:s',
            'label'    => 'config_system_format_datetime',
            'required' => true,
        ]);
        $configService->add([
            'name'     => 'system_format_date',
            'type'     => ConfigType::STRING,
            'group'    => 'date_format',
            'default'  => 'd.m.Y',
            'label'    => 'config_system_format_date',
            'required' => true,
        ]);
        $configService->add([
            'name'     => 'system_format_time',
            'type'     => ConfigType::STRING,
            'group'    => 'date_format',
            'default'  => 'H:i:s',
            'label'    => 'config_system_format_time',
            'required' => true,
        ]);
        $configService->add([
            'name'     => 'system_datetimepicker_format_datetime',
            'type'     => ConfigType::STRING,
            'group'    => 'date_format',
            'default'  => 'DD.MM.YYYY HH:mm',
            'label'    => 'config_system_format_datetime',
            'required' => true,
        ]);
        $configService->add([
            'name'     => 'system_datetimepicker_format_date',
            'type'     => ConfigType::STRING,
            'group'    => 'date_format',
            'default'  => 'DD.MM.YYYY',
            'label'    => 'config_system_format_date',
            'required' => true,
        ]);
        $configService->add([
            'name'     => 'system_datetimepicker_format_time',
            'type'     => ConfigType::STRING,
            'group'    => 'date_format',
            'default'  => 'HH:mm',
            'label'    => 'config_system_format_time',
            'required' => true,
        ]);
    }
}
