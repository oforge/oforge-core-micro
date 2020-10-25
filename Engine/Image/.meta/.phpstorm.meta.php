<?php

namespace PHPSTORM_META {

    if (function_exists('override')) {
        override(\Oforge\Engine\Core\Managers\Services\ServiceManager::get(0), map([
            'image'         => \Oforge\Engine\Image\Services\ImageService::class,
            'image.cleanup' => \Oforge\Engine\Image\Services\ImageCleanupService::class,
        ]));
    }

}
