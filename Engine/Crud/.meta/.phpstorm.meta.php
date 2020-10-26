<?php

namespace PHPSTORM_META {

    if (function_exists('override')) {
        /**
         * Services
         */
        override(\Oforge\Engine\Core\Managers\Services\ServiceManager::get(0), map([
            'crud' => \Oforge\Engine\Crud\Services\GenericCrudService::class,
        ]));
    }

}
