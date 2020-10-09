<?php

namespace PHPSTORM_META {

    if (function_exists('override')) {
        /**
         * Services
         */
        override(\Oforge\Engine\Core\Manager\Services\ServiceManager::get(0), map([
            'file'          => \Oforge\Engine\File\Services\FileService::class,
            'file.import'   => \Oforge\Engine\File\Services\FileImportService::class,
            'file.mimeType' => \Oforge\Engine\File\Services\AllowedFileMimeTypeService::class,
            // 'file.usage'    => \Oforge\Engine\File\Services\FileUsageService::class,
        ]));
        /**
         * Events
         */
        override(\Oforge\Engine\Core\Manager\Events\Event::create(0), map([
            \Oforge\Engine\File\Models\File::class . '::created' => '',
            \Oforge\Engine\File\Models\File::class . '::removed' => '',
        ]));
        override(\Oforge\Engine\Core\Manager\Events\EventManager::attach(0), map([
            \Oforge\Engine\File\Models\File::class . '::created' => '',
            \Oforge\Engine\File\Models\File::class . '::removed' => '',
        ]));
    }

}
