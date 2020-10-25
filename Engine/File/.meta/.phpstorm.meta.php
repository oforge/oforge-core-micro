<?php

namespace PHPSTORM_META {

    if (function_exists('override')) {
        /**
         * Services
         */
        override(\Oforge\Engine\Core\Managers\Services\ServiceManager::get(0), map([
            'file.access'          => \Oforge\Engine\File\Services\FileAccessService::class,
            'file.management'   => \Oforge\Engine\File\Services\FileManagementService::class,
            'file.mimeType' => \Oforge\Engine\File\Services\AllowedFileMimeTypeService::class,
            'file.usage'    => \Oforge\Engine\File\Services\FileUsageService::class,
        ]));
        /**
         * Events
         */
        override(\Oforge\Engine\Core\Managers\Events\Event::create(0), map([
            \Oforge\Engine\File\Models\File::class . '::created' => '',
            \Oforge\Engine\File\Models\File::class . '::removed' => '',
        ]));
        override(\Oforge\Engine\Core\Managers\Events\EventManager::attach(0), map([
            \Oforge\Engine\File\Models\File::class . '::created' => '',
            \Oforge\Engine\File\Models\File::class . '::removed' => '',
        ]));
    }

}
