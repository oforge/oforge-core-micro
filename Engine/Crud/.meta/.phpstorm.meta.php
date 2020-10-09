<?php

namespace PHPSTORM_META {

    if (function_exists('override')) {
        /**
         * Services
         */
        override(\Oforge\Engine\Core\Manager\Services\ServiceManager::get(0), map([
            'crud' => \Oforge\Engine\Crud\Service\GenericCrudService::class,
        ]));
    }

}
