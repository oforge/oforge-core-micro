<?php

namespace PHPSTORM_META {

    if (function_exists('override')) {
        override(\Oforge\Engine\Core\Manager\Services\ServiceManager::get(0), map([
            'url' => \Oforge\Engine\TemplateEngine\Extensions\Services\UrlService::class,
        ]));
    }

}
