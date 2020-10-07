<?php

namespace PHPSTORM_META {

    if (function_exists('override')) {
        override(\Oforge\Engine\Core\Manager\Services\ServiceManager::get(0), map([
            'file'          => \Oforge\Engine\File\Service\FileService::class,
            'file.mimeType' => \Oforge\Engine\File\Service\MimeTypeService::class,
        ]));
    }

}
