<?php

namespace PHPSTORM_META {

    if (function_exists('override')) {
        override(\Oforge\Engine\Core\Managers\Services\ServiceManager::get(0), map([
            'config'         => \Oforge\Engine\Core\Services\ConfigService::class,
            'encryption'     => \Oforge\Engine\Core\Services\EncryptionService::class,
            'endpoint'       => \Oforge\Engine\Core\Services\EndpointService::class,
            'middleware'     => \Oforge\Engine\Core\Services\MiddlewareService::class,
            'ping'           => \Oforge\Engine\Core\Services\PingService::class,
            'plugin.access'  => \Oforge\Engine\Core\Services\PluginAccessService::class,
            'plugin.state'   => \Oforge\Engine\Core\Services\PluginStateService::class,
            'redirect'       => \Oforge\Engine\Core\Services\RedirectService::class,
            'session'        => \Oforge\Engine\Core\Services\Session\SessionService::class,
            'store.keyvalue' => \Oforge\Engine\Core\Services\KeyValueStoreService::class,
            'token'          => \Oforge\Engine\Core\Services\TokenService::class,
        ]));

        registerArgumentsSet('oforge_flash_message_types', 'success', 'error', 'warning', 'info');
        expectedArguments(\Oforge\Engine\TemplateEngine\Core\Twig\TwigFlash::addMessage(), 0, argumentsSet('oforge_flash_message_types'));
        expectedArguments(\Oforge\Engine\TemplateEngine\Core\Twig\TwigFlash::addExceptionMessage(), 0, argumentsSet('oforge_flash_message_types'));

        override(\Oforge\Engine\Core\Abstracts\AbstractBootstrap::getConfiguration(0), map([
            'backendDashboardWidgets' => '',
            'backendNavigations'      => '',
            'settingGroups'           => '',
            'twigExtensions'          => '',
        ]));
        override(\Oforge\Engine\Core\Abstracts\AbstractBootstrap::setConfiguration(0), map([
            'backendDashboardWidgets' => '',
            'backendNavigations'      => '',
            'settingGroups'           => '',
            'twigExtensions'          => '',
        ]));
    }

}
